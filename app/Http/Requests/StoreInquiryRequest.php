<?php

namespace App\Http\Requests;

use App\Models\Inquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normalisasi nomor: buang spasi, strip, kurung, titik
        $this->merge([
            'nama' => trim((string) preg_replace('/\s+/u', ' ', (string) $this->input('nama'))),
            'whatsapp' => preg_replace('/[\s\-\(\)\.]/', '', (string) $this->input('whatsapp')),
        ]);
    }

    public function rules(): array
    {
        return [
            // Hanya huruf (termasuk beraksen), spasi, titik, apostrof, dan strip. Angka & simbol lain ditolak.
            'nama' => ['required', 'string', 'max:100', "regex:/^\p{L}[\p{L}\s.'’\-]*$/u"],
            'whatsapp' => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'jenis_pesan' => ['required', Rule::in([Inquiry::PEMESANAN, Inquiry::PERTANYAAN])],
            'isi_pesan' => ['nullable', 'string', 'max:1000'],
            'website' => ['prohibited'], // honeypot: harus kosong
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'nama.regex' => 'Nama hanya boleh berisi huruf (tanpa angka atau simbol).',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp harus berupa angka yang valid (contoh: 082336970925).',
            'email.email' => 'Format email tidak valid.',
            'jenis_pesan.required' => 'Pilih jenis pesan.',
            'jenis_pesan.in' => 'Jenis pesan tidak valid.',
            'isi_pesan.max' => 'Isi pesan maksimal 1000 karakter.',
            'website.prohibited' => 'Permintaan tidak valid.',
        ];
    }

    /** Kembali ke form (anchor) saat validasi gagal. */
    protected function getRedirectUrl(): string
    {
        return url()->previous() . '#form-pemesanan';
    }
}
