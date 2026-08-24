<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Alter columns to TEXT to accommodate encrypted payloads
        Schema::table('companies', function (Blueprint $table) {
            $table->text('tax_number')->nullable()->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->text('transaction_reference')->nullable()->change();
        });

        // 2. Encrypt existing plaintext data
        // For companies.tax_number
        $companies = DB::table('companies')->whereNotNull('tax_number')->get(['id', 'tax_number']);
        foreach ($companies as $company) {
            try {
                // Check if already encrypted (this will throw DecryptException if it's plaintext)
                Crypt::decryptString($company->tax_number);
            } catch (DecryptException $e) {
                // If it's not encrypted, encrypt it
                DB::table('companies')
                    ->where('id', $company->id)
                    ->update(['tax_number' => Crypt::encryptString($company->tax_number)]);
            }
        }

        // For payments.transaction_reference
        $payments = DB::table('payments')->whereNotNull('transaction_reference')->get(['id', 'transaction_reference']);
        foreach ($payments as $payment) {
            try {
                Crypt::decryptString($payment->transaction_reference);
            } catch (DecryptException $e) {
                DB::table('payments')
                    ->where('id', $payment->id)
                    ->update(['transaction_reference' => Crypt::encryptString($payment->transaction_reference)]);
            }
        }

        // For settings.value
        $settings = DB::table('settings')->whereNotNull('value')->get(['id', 'value']);
        foreach ($settings as $setting) {
            try {
                Crypt::decryptString($setting->value);
            } catch (DecryptException $e) {
                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['value' => Crypt::encryptString($setting->value)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Decrypt data before changing column type back
        $companies = DB::table('companies')->whereNotNull('tax_number')->get(['id', 'tax_number']);
        foreach ($companies as $company) {
            try {
                $decrypted = Crypt::decryptString($company->tax_number);
                DB::table('companies')
                    ->where('id', $company->id)
                    ->update(['tax_number' => substr($decrypted, 0, 255)]); // Ensure it fits in varchar
            } catch (DecryptException $e) {
                // Already plaintext or corrupted
            }
        }

        $payments = DB::table('payments')->whereNotNull('transaction_reference')->get(['id', 'transaction_reference']);
        foreach ($payments as $payment) {
            try {
                $decrypted = Crypt::decryptString($payment->transaction_reference);
                DB::table('payments')
                    ->where('id', $payment->id)
                    ->update(['transaction_reference' => substr($decrypted, 0, 255)]);
            } catch (DecryptException $e) {
                // Already plaintext
            }
        }

        $settings = DB::table('settings')->whereNotNull('value')->get(['id', 'value']);
        foreach ($settings as $setting) {
            try {
                $decrypted = Crypt::decryptString($setting->value);
                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['value' => $decrypted]);
            } catch (DecryptException $e) {
                // Already plaintext
            }
        }

        // Change columns back to string
        Schema::table('payments', function (Blueprint $table) {
            $table->string('transaction_reference')->nullable()->change();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('tax_number')->nullable()->change();
        });
    }
};
