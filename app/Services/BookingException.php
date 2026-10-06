<?php

namespace App\Services;

use Exception;

// Thrown when a booking or status change is not allowed. The message is safe to show to the user.
class BookingException extends Exception
{
}
