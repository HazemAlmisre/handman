<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WalletHistoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('wallet_histories')->delete();
        
        \DB::table('wallet_histories')->insert(array (
            0 => 
            array (
                'activity_data' => '{"user_id":4,"credit_debit_amount":10,"amount":990,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹10.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-24 17:24:58',
                'datetime' => '2024-07-24 17:24:58',
                'id' => 1,
                'updated_at' => '2024-07-24 17:24:58',
                'user_id' => 4,
            ),
            1 => 
            array (
                'activity_data' => '{"user_id":3,"credit_debit_amount":1,"amount":2199.2385,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹1.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-24 17:27:32',
                'datetime' => '2024-07-24 17:27:32',
                'id' => 2,
                'updated_at' => '2024-07-24 17:27:32',
                'user_id' => 3,
            ),
            2 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"provider_name":"","amount":2209.2385,"transaction_id":null,"transaction_type":"Credit","credit_debit_amount":10}',
                'activity_message' => 'Your wallet has been successfully topped up. Your new wallet balance is ₹10.00',
                'activity_type' => 'wallet_top_up',
                'created_at' => '2024-07-24 17:28:04',
                'datetime' => '2024-07-24 17:28:04',
                'id' => 3,
                'updated_at' => '2024-07-24 17:28:04',
                'user_id' => 3,
            ),
            3 => 
            array (
                'activity_data' => '{"user_id":4,"credit_debit_amount":11,"amount":979,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹11.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-24 17:38:50',
                'datetime' => '2024-07-24 17:38:50',
                'id' => 4,
                'updated_at' => '2024-07-24 17:38:50',
                'user_id' => 4,
            ),
            4 => 
            array (
                'activity_data' => '{"user_id":4,"credit_debit_amount":8,"amount":971,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹8.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-24 17:41:29',
                'datetime' => '2024-07-24 17:41:29',
                'id' => 5,
                'updated_at' => '2024-07-24 17:41:29',
                'user_id' => 4,
            ),
            5 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"provider_name":"","amount":2219.2385,"transaction_id":null,"transaction_type":"Credit","credit_debit_amount":10}',
                'activity_message' => 'Your wallet has been successfully topped up. Your new wallet balance is ₹10.00',
                'activity_type' => 'wallet_top_up',
                'created_at' => '2024-07-24 17:42:41',
                'datetime' => '2024-07-24 17:42:41',
                'id' => 6,
                'updated_at' => '2024-07-24 17:42:41',
                'user_id' => 3,
            ),
            6 => 
            array (
                'activity_data' => '{"user_id":3,"credit_debit_amount":10,"amount":2209.2385,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹10.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-24 17:43:02',
                'datetime' => '2024-07-24 17:43:02',
                'id' => 7,
                'updated_at' => '2024-07-24 17:43:02',
                'user_id' => 3,
            ),
            7 => 
            array (
                'activity_data' => '{"user_id":3,"credit_debit_amount":25,"amount":2184.2385,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹25.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-24 17:43:14',
                'datetime' => '2024-07-24 17:43:14',
                'id' => 8,
                'updated_at' => '2024-07-24 17:43:14',
                'user_id' => 3,
            ),
            8 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"provider_name":"","amount":2196.2385,"transaction_id":null,"transaction_type":"Credit","credit_debit_amount":12}',
                'activity_message' => 'Your wallet has been successfully topped up. Your new wallet balance is ₹12.00',
                'activity_type' => 'wallet_top_up',
                'created_at' => '2024-07-24 17:45:30',
                'datetime' => '2024-07-24 17:45:30',
                'id' => 9,
                'updated_at' => '2024-07-24 17:45:30',
                'user_id' => 3,
            ),
            9 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"provider_name":"","amount":12196.2385,"transaction_id":null,"transaction_type":"Credit","credit_debit_amount":10000}',
                'activity_message' => 'Your wallet has been successfully topped up. Your new wallet balance is ₹10000.00',
                'activity_type' => 'wallet_top_up',
                'created_at' => '2024-07-24 17:46:12',
                'datetime' => '2024-07-24 17:46:12',
                'id' => 10,
                'updated_at' => '2024-07-24 17:46:12',
                'user_id' => 3,
            ),
            10 => 
            array (
                'activity_data' => '{"user_id":4,"credit_debit_amount":21,"amount":950,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹21.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-24 17:46:56',
                'datetime' => '2024-07-24 17:46:56',
                'id' => 11,
                'updated_at' => '2024-07-24 17:46:56',
                'user_id' => 4,
            ),
            11 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"amount":11142.2385,"service_name":"\\u00c9lectricien Auto","user_name":null,"credit_debit_amount":1054,"transaction_type":"Debit"}',
                'activity_message' => 'Paid For Booking #231',
                'activity_type' => 'paid_for_booking',
                'created_at' => '2024-07-25 02:02:18',
                'datetime' => '2024-07-25 02:02:18',
                'id' => 12,
                'updated_at' => '2024-07-25 02:02:18',
                'user_id' => 3,
            ),
            12 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"amount":11138.288499999999,"service_name":"Circuit Repair","user_name":null,"credit_debit_amount":3.95,"transaction_type":"Debit"}',
                'activity_message' => 'Paid For Booking #234',
                'activity_type' => 'paid_for_booking',
                'created_at' => '2024-07-25 05:25:19',
                'datetime' => '2024-07-25 05:25:19',
                'id' => 13,
                'updated_at' => '2024-07-25 05:25:19',
                'user_id' => 3,
            ),
            13 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"amount":11137.5365,"service_name":"Office Cleaning","user_name":null,"credit_debit_amount":0.752,"transaction_type":"Debit"}',
                'activity_message' => 'Paid For Booking #236',
                'activity_type' => 'paid_for_booking',
                'created_at' => '2024-07-25 09:45:28',
                'datetime' => '2024-07-25 09:45:28',
                'id' => 14,
                'updated_at' => '2024-07-25 09:45:28',
                'user_id' => 3,
            ),
            14 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"amount":11138.2885,"credit_debit_amount":0.752,"transaction_type":"Credit"}',
                'activity_message' => 'Refund From Booking #236',
                'activity_type' => 'wallet_refund',
                'created_at' => '2024-07-25 09:46:00',
                'datetime' => '2024-07-25 09:46:00',
                'id' => 15,
                'updated_at' => '2024-07-25 09:46:00',
                'user_id' => 3,
            ),
            15 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"amount":11122.488500000001,"service_name":"Circuit Repair","user_name":null,"credit_debit_amount":15.8,"transaction_type":"Debit"}',
                'activity_message' => 'Paid For Booking #234',
                'activity_type' => 'paid_for_booking',
                'created_at' => '2024-07-25 13:39:46',
                'datetime' => '2024-07-25 13:39:46',
                'id' => 16,
                'updated_at' => '2024-07-25 13:39:46',
                'user_id' => 3,
            ),
            16 => 
            array (
                'activity_data' => '{"user_id":3,"credit_debit_amount":10,"amount":11112.4885,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹10.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-25 14:33:48',
                'datetime' => '2024-07-25 14:33:48',
                'id' => 17,
                'updated_at' => '2024-07-25 14:33:48',
                'user_id' => 3,
            ),
            17 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"provider_name":"","amount":11122.4885,"transaction_id":null,"transaction_type":"Credit","credit_debit_amount":10}',
                'activity_message' => 'Your wallet has been successfully topped up. Your new wallet balance is ₹10.00',
                'activity_type' => 'wallet_top_up',
                'created_at' => '2024-07-25 14:34:04',
                'datetime' => '2024-07-25 14:34:04',
                'id' => 18,
                'updated_at' => '2024-07-25 14:34:04',
                'user_id' => 3,
            ),
            18 => 
            array (
                'activity_data' => '{"user_id":4,"credit_debit_amount":5,"amount":945,"transaction_type":"Debit"}',
                'activity_message' => 'Your Withdraw Money has been successfully Transfer ₹5.00',
                'activity_type' => 'withdraw_money',
                'created_at' => '2024-07-25 14:34:46',
                'datetime' => '2024-07-25 14:34:46',
                'id' => 19,
                'updated_at' => '2024-07-25 14:34:46',
                'user_id' => 4,
            ),
            19 => 
            array (
                'activity_data' => '{"title":"Pedro Norris User Wallet","user_id":3,"amount":11121.736499999999,"service_name":"Office Cleaning","user_name":null,"credit_debit_amount":0.752,"transaction_type":"Debit"}',
                'activity_message' => 'Paid For Booking #242',
                'activity_type' => 'paid_for_booking',
                'created_at' => '2024-07-25 15:02:14',
                'datetime' => '2024-07-25 15:02:14',
                'id' => 20,
                'updated_at' => '2024-07-25 15:02:14',
                'user_id' => 3,
            ),
        ));
        
        
    }
}