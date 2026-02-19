<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wallet extends Model {
    protected static $table_name = 'mtts_wallets';

    public static function get_by_student( $student_id ) {
        global $wpdb;
        $table = self::get_table_name();
        $wallet = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE student_id = %d", $student_id ) );

        if ( ! $wallet ) {
            // Auto-create wallet if not exists
            $wpdb->insert( $table, array( 'student_id' => $student_id, 'balance' => 0.00 ) );
            return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE student_id = %d", $student_id ) );
        }

        return $wallet;
    }

    public static function credit( $student_id, $amount, $description = '', $reference = '' ) {
        $wallet = self::get_by_student( $student_id );
        $new_balance = $wallet->balance + $amount;
        
        return self::update_balance( $wallet->id, $new_balance, 'credit', $amount, $description, $reference );
    }

    public static function debit( $student_id, $amount, $description = '', $reference = '' ) {
        $wallet = self::get_by_student( $student_id );
        
        if ( $wallet->balance < $amount ) {
            return false; // Insufficient funds
        }

        $new_balance = $wallet->balance - $amount;
        
        return self::update_balance( $wallet->id, $new_balance, 'debit', $amount, $description, $reference );
    }

    private static function update_balance( $wallet_id, $new_balance, $type, $amount, $description, $reference ) {
        global $wpdb;
        $wallet_table = self::get_table_name();
        $trx_table = $wpdb->prefix . 'mtts_wallet_transactions';

        $wpdb->query( 'START TRANSACTION' );

        $updated = $wpdb->update( 
            $wallet_table, 
            array( 'balance' => $new_balance ), 
            array( 'id' => $wallet_id ) 
        );

        if ( $updated === false ) {
            $wpdb->query( 'ROLLBACK' );
            return false;
        }

        $inserted = $wpdb->insert( 
            $trx_table, 
            array( 
                'wallet_id' => $wallet_id, 
                'type' => $type, 
                'amount' => $amount, 
                'description' => $description, 
                'reference' => $reference 
            ) 
        );

        if ( $inserted === false ) {
            $wpdb->query( 'ROLLBACK' );
            return false;
        }

        $wpdb->query( 'COMMIT' );
        return true;
    }

    public static function transfer( $sender_student_id, $receiver_matric, $amount, $description = '' ) {
        global $wpdb;
        $wallet_table = self::get_table_name();
        $trx_table = $wpdb->prefix . 'mtts_wallet_transactions';

        // 1. Find Sender Wallet
        $sender_wallet = self::get_by_student( $sender_student_id );
        if ( ! $sender_wallet ) {
            return array( 'success' => false, 'message' => 'Sender wallet not found.' );
        }

        if ( $sender_wallet->balance < $amount ) {
            return array( 'success' => false, 'message' => 'Insufficient funds.' );
        }

        // 2. Find Receiver Student/Wallet
        $receiver_student = \MttsLms\Models\Student::get_by_matric( $receiver_matric );
        if ( ! $receiver_student ) {
            return array( 'success' => false, 'message' => 'Receiver not found (Invalid Matric Number).' );
        }

        if ( $receiver_student->id == $sender_student_id ) {
            return array( 'success' => false, 'message' => 'You cannot transfer funds to yourself.' );
        }

        $receiver_wallet = self::get_by_student( $receiver_student->id );

        // 3. Process Transaction
        $wpdb->query( 'START TRANSACTION' );

        // Debit Sender
        $sender_updated = $wpdb->update( 
            $wallet_table, 
            array( 'balance' => $sender_wallet->balance - $amount ), 
            array( 'id' => $sender_wallet->id ) 
        );

        if ( $sender_updated === false ) {
            $wpdb->query( 'ROLLBACK' );
            return array( 'success' => false, 'message' => 'Internal error during debit.' );
        }

        $wpdb->insert( $trx_table, array( 
            'wallet_id'   => $sender_wallet->id, 
            'type'        => 'debit', 
            'amount'      => $amount, 
            'description' => $description ?: "Transfer to {$receiver_matric}", 
            'reference'   => 'TRANS_' . strtoupper( uniqid() )
        ) );

        // Credit Receiver
        $receiver_updated = $wpdb->update( 
            $wallet_table, 
            array( 'balance' => $receiver_wallet->balance + $amount ), 
            array( 'id' => $receiver_wallet->id ) 
        );

        if ( $receiver_updated === false ) {
            $wpdb->query( 'ROLLBACK' );
            return array( 'success' => false, 'message' => 'Internal error during credit.' );
        }

        $wpdb->insert( $trx_table, array( 
            'wallet_id'   => $receiver_wallet->id, 
            'type'        => 'credit', 
            'amount'      => $amount, 
            'description' => $description ?: "Transfer from Sender #{$sender_student_id}", 
            'reference'   => 'TRANS_' . strtoupper( uniqid() )
        ) );

        $wpdb->query( 'COMMIT' );
        return array( 'success' => true, 'message' => 'Funds transferred successfully.' );
    }

    public static function get_history( $student_id, $limit = 20 ) {
        global $wpdb;
        $wallet = self::get_by_student( $student_id );
        $trx_table = $wpdb->prefix . 'mtts_wallet_transactions';
        
        return $wpdb->get_results( $wpdb->prepare( 
            "SELECT * FROM {$trx_table} WHERE wallet_id = %d ORDER BY created_at DESC LIMIT %d", 
            $wallet->id, $limit 
        ) );
    }
}
