<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'subject',
        'body_html',
        'available_variables',
        'is_active',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Render subject with supplied parameters.
     */
    public function renderSubject(array $data = []): string
    {
        $subject = $this->subject;
        foreach ($data as $key => $val) {
            $subject = str_replace('{' . $key . '}', (string) $val, $subject);
        }
        return $subject;
    }

    /**
     * Render body html with supplied parameters.
     */
    public function renderBody(array $data = []): string
    {
        $body = $this->body_html;
        foreach ($data as $key => $val) {
            $body = str_replace('{' . $key . '}', (string) $val, $body);
        }
        return $body;
    }
}

