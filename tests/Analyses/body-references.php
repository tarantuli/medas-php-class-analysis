<?php

namespace App\Jobs;

use App\Models\Order;

class ProcessOrder
{
    public function handle(): void
    {
        $order = new Order();
        $invoice = new \App\Billing\Invoice();

        if ($order instanceof Refundable) {
            Refunder::refund($order);
        }

        $class = Shipment::class;
        $status = Status::Paid;
        $value = $this->service::CONSTANT;

        try {
            $order->ship();
        } catch (ShippingFailed | Sub\Timeout $e) {
            report($e);
        } catch (Fatal) {
        }

        $callback = function (Order $order): Result {
            return new Result();
        };
        $mapper = fn(Line $line): Total => new Total($line);

        $label = $order->paid ? PAID_LABEL : OPEN_LABEL;
        $made = new Shipment(carrier: Carrier::default(), weight: 10);
    }
}
