<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv(false))->loadEnv(dirname(__DIR__).'/.env');


$stripeApiKey = $_ENV['STRIPE_SECRET'];

 \Stripe\Stripe::setApiKey($stripeApiKey );
 
 if($_SERVER["REQUEST_METHOD"] == "POST"  )
 {
    if(isset($_POST["email"]) && !empty($_POST["email"]))
    {

     $email = $_POST["email"];
      
     $stripe = new \Stripe\StripeClient($stripeApiKey);
     $customers =  $stripe->customers->all([["email" => $email]]);

     $customer_data = $customers->data;
     $customer_id = $customer_data[0]->id;


      if($customer_id) {
            // Authenticate your user.
         $session = \Stripe\BillingPortal\Session::create([
            'customer' => $customer_id,
            'return_url' => 'https://rushfiles.webflow.io/',
            ]);

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom("invitation@rushfiles.com", "RushFiles User");
            $email->setSubject("Sending with Twilio SendGrid is Fun");
            $email->addTo("saranya@cantilever.co", "Example User");
            $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
            $email->addContent(
                "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
            );
           $sendgrid = new \SendGrid($_ENV['SENDGRID_APIKEY']);
            try {

                $response = $sendgrid->send($email);
                print $response->statusCode() . "\n";
                print_r($response->headers());
                print $response->body() . "\n";
                echo "Your IP address is ".$_SERVER['REMOTE_ADDR'];
            } catch (Exception $e) {
                echo 'Caught exception: '. $e->getMessage() ."\n";
            } 
          
       
       //  header("Location: " . $session->url);
      }
      else
      {
          echo "No Customer found with the given email. Please check again";
      }
       
    }
    else 
    {
        echo "Please go back and enter the email address";
    }
 }

