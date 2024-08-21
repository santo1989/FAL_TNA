<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuyerWiseTnaSummary extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;

    public $buyers;
    public $columns;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($buyers, $columns)
    {
        $this->buyers = $buyers;
        $this->columns = $columns;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Buyer-Wise Pending TNA Summary')
        ->view('emails.buyer_wise_tna_summary')
        ->with([
            'buyers' => $this->buyers,
            'columns' => $this->columns
        ]);
    }

}
