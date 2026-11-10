<?php

namespace App\Services;

use App\Models\User;
use App\Models\MailSetting;
use App\Models\EmailTemplate;
use App\Mail\DynamicNotificationMail;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send in-app and optional email notification to a user or multiple users.
     *
     * @param User|int|Collection|array $recipients
     * @param string $title
     * @param string $message
     * @param string|null $url
     * @param string $category 'course'|'webinar'|'certificate'|'evaluation'|'approval'|'profile'|'system'
     * @param string $icon 'graduation-cap'|'video'|'award'|'clipboard'|'building'|'user'|'bell'
     * @param array $templateVariables Extra variables for email template rendering
     * @return void
     */
    public static function send(
        User|int|Collection|array $recipients,
        string $title,
        string $message,
        ?string $url = '#',
        string $category = 'system',
        string $icon = 'bell',
        array $templateVariables = []
    ): void {
        $payload = [
            'title'    => $title,
            'message'  => $message,
            'url'      => $url ?: '#',
            'category' => $category,
            'icon'     => $icon,
        ];

        if ($recipients instanceof User) {
            $recipients->notify(new GeneralNotification($payload));
            self::dispatchEmailIfActive($recipients, $category, $title, $message, $url, $templateVariables);
            return;
        }

        if (is_numeric($recipients)) {
            $user = User::find($recipients);
            if ($user) {
                $user->notify(new GeneralNotification($payload));
                self::dispatchEmailIfActive($user, $category, $title, $message, $url, $templateVariables);
            }
            return;
        }

        if (is_array($recipients) || $recipients instanceof Collection) {
            foreach ($recipients as $recipient) {
                self::send($recipient, $title, $message, $url, $category, $icon, $templateVariables);
            }
        }
    }

    /**
     * Send notification using an explicit template code (1 of the 7 official templates).
     */
    public static function sendTemplate(
        User|int $recipient,
        string $templateCode,
        array $variables = [],
        ?string $url = '#',
        string $icon = 'bell'
    ): void {
        $user = is_numeric($recipient) ? User::find($recipient) : $recipient;
        if (!$user) return;

        $template = EmailTemplate::where('code', $templateCode)->where('is_active', true)->first();

        // Standard variables auto-injection
        $defaultVars = [
            'nama_peserta' => $user->name,
            'nip'          => $user->nip ?: '-',
            'instansi'     => $user->opd_name ?: 'Pemerintah Kabupaten Berau',
            'link_kegiatan'=> $url ?: '#',
        ];
        $mergedVars = array_merge($defaultVars, $variables);

        $title = $template ? $template->renderSubject($mergedVars) : 'Pemberitahuan Sistem';
        $message = $template ? strip_tags(substr($template->renderBody($mergedVars), 0, 180)) . '...' : 'Pemberitahuan baru.';

        $payload = [
            'title'    => $title,
            'message'  => $message,
            'url'      => $url ?: '#',
            'category' => $template?->category ?? 'system',
            'icon'     => $icon,
        ];

        // 1. In-app database notification
        $user->notify(new GeneralNotification($payload));

        // 2. Email dispatch if SMTP active
        if ($template) {
            self::sendCustomEmail($user, $template, $mergedVars, $url);
        }
    }

    /**
     * Send notification to all Administrators & Super Admins.
     */
    public static function notifyAdmins(string $title, string $message, ?string $url = '#', string $category = 'approval'): void
    {
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        self::send($admins, $title, $message, $url, $category, 'building');
    }

    /**
     * Dispatch email if SMTP setting is active.
     */
    private static function dispatchEmailIfActive(
        User $user,
        string $category,
        string $title,
        string $message,
        ?string $url,
        array $extraVars = []
    ): void {
        try {
            $mailSetting = MailSetting::getSettings();
            if (!$mailSetting->is_active || empty($user->email)) {
                return;
            }

            // Map category to template code
            $code = match ($category) {
                'course'      => 'registration_success',
                'approval'    => 'registration_approved',
                'webinar'     => 'activity_schedule',
                'evaluation'  => 'evaluation_reminder',
                'certificate' => 'certificate_available',
                default       => null,
            };

            $template = $code ? EmailTemplate::where('code', $code)->where('is_active', true)->first() : null;

            $vars = array_merge([
                'nama_peserta'     => $user->name,
                'nip'              => $user->nip ?: '-',
                'instansi'         => $user->opd_name ?: 'Pemerintah Kabupaten Berau',
                'nama_kegiatan'    => $title,
                'nomor_tiket'      => 'REG-' . strtoupper(substr(md5($user->id . time()), 0, 8)),
                'link_kegiatan'    => $url ?: '#',
                'link_sertifikat'  => $url ?: '#',
                'link_evaluasi'    => $url ?: '#',
                'link_webinar'     => $url ?: '#',
                'bobot_jp'         => '2',
                'tanggal_kegiatan' => date('d F Y'),
                'waktu_mulai'      => '09:00 WITA',
                'metode_kegiatan'  => 'E-Learning Mandiri & Tatap Muka',
                'lokasi_kegiatan'  => 'Portal LMS KAB Berau',
            ], $extraVars);

            if ($template) {
                self::sendCustomEmail($user, $template, $vars, $url);
            } else {
                // Fallback direct email
                $mailSetting->applyToConfig();
                $bodyHtml = "<p>Yth. <strong>{$user->name}</strong>,</p><p>{$message}</p>" . 
                    ($url && $url !== '#' ? "<p style='margin-top:20px;'><a href='{$url}' style='background:#0e6c84;color:#fff;padding:10px 20px;text-decoration:none;border-radius:8px;font-weight:bold;'>Buka Halaman &rarr;</a></p>" : "");
                Mail::to($user->email)->send(new DynamicNotificationMail($title, $bodyHtml, $url));
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirimkan notifikasi email SMTP: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);
        }
    }

    /**
     * Send custom email using specific template.
     */
    public static function sendCustomEmail(User $user, EmailTemplate $template, array $variables, ?string $url = null): void
    {
        try {
            $mailSetting = MailSetting::getSettings();
            $mailSetting->applyToConfig();

            $subject = $template->renderSubject($variables);
            $bodyHtml = $template->renderBody($variables);

            Mail::to($user->email)->send(new DynamicNotificationMail($subject, $bodyHtml, $url));
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim email template [{$template->code}] ke [{$user->email}]: " . $e->getMessage());
        }
    }
}
