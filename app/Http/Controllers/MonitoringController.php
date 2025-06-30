<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Pelanggan; // Use Router model for router data
use Illuminate\Http\Request;
use App\Services\RouterosAPI;

class MonitoringController extends Controller
{
    // Function to connect to MikroTik Router
    private function mikrotik($id)
    {
        // Fetch server details using the ID
        $server = Server::findOrFail($id);

        // MikroTik connection credentials
        $ip = $server->ip;
        $user = $server->user;
        $pass = $server->password;



        // Initialize RouterosAPI
        $mikrotik = new RouterosAPI();
        $mikrotik->debug = false;

        // Connect to MikroTik device
        if ($mikrotik->connect($ip, $user, $pass)) {
            return $mikrotik;
        }
        return null;
    }

    // Ping function to check router status
    private function ping($mikrotik, $ip)
    {
        $exe = $mikrotik->comm('/ping', [
            'address' => $ip,
            'count' => 1
        ]);
        return $exe;
    }





    // Monitoring function to check the router statuses
    public function index($id)
    {
        // Connect to MikroTik router
        $mikrotik = $this->mikrotik($id);

        if ($mikrotik) {
            // Fetch all routers for the specified server
            $routers = Pelanggan::where('id_server', $id)->get();  // Ensure you're using the correct model
            $routerStatus = [];
            $routerLoss = [];

            foreach ($routers as $router) {
                // Ping the router and check the status
                $ping = $this->ping($mikrotik, $router->ip_router);

                // Check if ping returned a valid response
                if (isset($ping[0]) && isset($ping[0]['received'])) {
                    // Determine status based on ping result
                    $status = ($ping[0]['received'] == 1) ? 'green' : 'red';
                } else {
                    // If ping fails or no data, mark as red (loss)
                    $status = 'red';
                }

                // Store router status
                $routerStatus[] = [
                    'name' => $router->nama,
                    'ip' => $router->ip_router,
                    'parent' => $router->ip_parent_router,
                    'status' => $status
                ];

                // If router status is red, add it to the loss list
                if ($status == 'red') {
                    $routerLoss[] = '-' . $router->id_server . '/' . $router->ip_router . '/' . $router->name . '/ ** Loss';
                }
            }

            // Disconnect from MikroTik
            $mikrotik->disconnect();

            // Send Telegram notification if there is a router loss
            // if (count($routerLoss) > 0) {
            //     $this->sendTelegramMessage($routerLoss);
            // }

            // Return the response with router status
            return response()->json([
                'routerStatus' => $routerStatus,
                'server' => Server::find($id)
            ]);
        } else {
            // If unable to connect to MikroTik
            return response()->json(['error' => 'Unable to connect to MikroTik'], 500);
        }
    }


    // Function to send message to Telegram if routers are down
    // private function sendTelegramMessage($routerLoss)
    // {
    //     $url = "https://api.telegram.org/bot1614882691:AAHWOJN0w_tvmwU1QokpvMWCNv8HLrXakRM/sendMessage?chat_id=-517279951&";
    //     $text = "text=" . date('d-m-Y') . "\n\n" . implode("\n", $routerLoss);
    //     file_get_contents($url . $text);
    // }
}
