<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class UpdateExpiredAddOns extends Command
{
    protected $signature = 'addons:update-expired';
    protected $description = 'Update expired add-on status to 2';

    public function handle()
    {
        $bookings = Booking::whereNotNull('add_on')->get();
        $today = Carbon::today();

        foreach ($bookings as $booking) {
            $rawAddOns = explode(',', $booking->add_on);
            $temp = implode(',', $rawAddOns);
            preg_match_all('/\{.*?\}/', $temp, $matches);

            $parsedAddOns = [];

            foreach ($matches[0] as $addOnStr) {
                $fixedStr = str_replace(['{', '}'], ['', ''], $addOnStr);
                $parts = explode(',', $fixedStr);

                $addOn = [];
                foreach ($parts as $part) {
                    [$key, $value] = explode(':', $part, 2);
                    $addOn[trim($key)] = trim($value, " \t\n\r\0\x0B\"[]");
                }

           
                $date = null;
                if (isset($addOn['date'])) {
                    $decodedDate = json_decode($addOn['date'], true);
                    if (is_array($decodedDate)) {
                        $decodedDate = reset($decodedDate);
                    }

                    try {
                        $date = Carbon::parse($decodedDate);
                    } catch (\Exception $e) {
                        $date = null;
                    }
                }

    
                $addOn['status'] = $date && $date->greaterThanOrEqualTo($today) ? 1 : 2;
                $parsedAddOns[] = $addOn;
            }

         
            $booking->add_on = json_encode($parsedAddOns);
            $booking->save();
        }

        $this->info("Expired add-ons updated successfully.");
    }
}
