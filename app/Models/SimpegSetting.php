<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimpegSetting extends Model
{
    protected $fillable = [
        'is_active',
        'provider_name',
        'server_url',
        'auth_type',
        'api_key',
        'header_name',
        'endpoint_all',
        'endpoint_single',
        'http_method',
        'field_mapping',
        'data_wrapper',
        'default_password',
        'auto_sync_on_login',
        'last_sync_at',
        'last_sync_count',
        'last_sync_message',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'auto_sync_on_login' => 'boolean',
        'field_mapping'      => 'array',
        'last_sync_at'       => 'datetime',
        'last_sync_count'    => 'integer',
    ];

    /**
     * Dapatkan instance pengaturan SIMPEG (Singleton pattern)
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate([], [
            'is_active'          => false,
            'provider_name'      => 'SIMPEG BKPSDM Kabupaten',
            'server_url'         => 'https://simpeg.kabupaten.go.id/api/v1',
            'auth_type'          => 'bearer',
            'api_key'            => '',
            'header_name'        => 'Authorization',
            'endpoint_all'       => '/pegawai',
            'endpoint_single'    => '/pegawai/{nip}',
            'http_method'        => 'GET',
            'data_wrapper'       => 'data',
            'default_password'   => 'Asn12345!',
            'auto_sync_on_login' => false,
            'field_mapping'      => self::defaultFieldMapping(),
        ]);
    }

    /**
     * Mapping default kolom JSON SIMPEG ke atribut User LMS
     */
    public static function defaultFieldMapping(): array
    {
        return [
            'nip'              => 'nip',
            'name'             => 'nama_lengkap',
            'email'            => 'email',
            'opd_name'         => 'nama_opd',
            'bagian'           => 'bagian',
            'jabatan'          => 'jabatan',
            'pangkat_golongan' => 'pangkat_golongan',
            'phone'            => 'nomor_hp',
        ];
    }

    /**
     * Helper untuk mendapatkan mapping yang aman (fallback ke default jika kosong)
     */
    public function getActiveFieldMapping(): array
    {
        $current = $this->field_mapping;
        if (empty($current) || !is_array($current)) {
            return self::defaultFieldMapping();
        }

        return array_merge(self::defaultFieldMapping(), $current);
    }
}
