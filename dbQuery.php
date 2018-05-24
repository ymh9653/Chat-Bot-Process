<?php
    function processMessage($request) { 
            $dbString = "postgres://ytchabybvuaeog:f457e5a75151626f6a23493485673b3ea75595a7dcbcfd3b7994fc9d7f1e53d5@ec2-54-235-66-24.compute-1.amazonaws.com:5432/d5tfc77ecvjh96"            ;
        
                $db = new PDO($dbString); 
                
                if ($request["result"]["action"] == "DBLink" ) {
                    $papercode = $request["result"]["parameters"]["paper1"];
                    
                    $query = "SELECT * FROM papers WHERE papercode LIKE '%$papercode'";
                    $dbres = $db->query($query);

                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $pLevel = $row["paperlevel"];
                        $pCode = $row["papercode"];
                        $pName = $row["papername"];
                        $pPoints = $row["paperpoints"];
                        $pCos = $row["papercos"];
                        $pPres = $row["paperpros"];
                    }

                    $result->closeCursor();

                    sendMessage( array (
                        "source" => $update["result"]["source"],
                        "speech" => $pName . ", worth " . $pPoints . " points",
                        "displayText" => $pName . ", worth " . $pPoints . " points",
                        "contextOut" => array()
                    ) );
                }
            }
        
        function sendMessage($parameters) {
            echo json_encode($parameters);
        }
        
        $request_response = file_get_contents("php://input");
        $request = json_decode($request_response, true);
        if (isset($request["result"]["action"])) {
            processMessage($request);
        }
    
?>