<?php
    function processPres($string) {
        if ($string == "N/A") {
            $presText = "It does not have any Pre-Requisite Papers";
        } else {
            if (substr($string, 0, 1) == "(") {

                    $opPre1 = substr($string, 1, 7);
                    $opPre2 = substr($string, 12, 7);

                    if (strLen($string) > 20) {
                            if (substr($string, 19, 1) == ")") {
                                    $opPre3 = substr($string, 22, 7);
                                    $presText = "The PreReqs of the paper are one of ".$opPre1." or ". $opPre2.", and ".$opPre3;
                            } else {
                                    $opPre3 = substr($string, 23, 7);
                                    $presText = "The PreReqs of the paper either one of ".$opPre1.", ". $opPre2." or ".$opPre3;
                            }

                    } else {
                            $presText = "The PreReq of the paper are either ".$opPre1." or ".$opPre2;
                    }
            } else {
                    $opPre1 = substr($string, 0, 7);
                    if (substr($string, 7, 1) == ",") {
                            $opPre2 = substr($string, 9, 7); 
                            $presText = "The PreReqs for this paper are ".$opPre1." and ".$opPre2;
                    } else {
                            $presText = "The only PreReq for this paper is ".$opPre1;
                    }
            }           
        } 

        return $presText;
    }

    function processMessage($request) {         
            $dbString = "pgsql:"
                        . "host=ec2-54-235-66-24.compute-1.amazonaws.com;"
                        . "dbname=d5tfc77ecvjh96;"
                        . "user=ytchabybvuaeog;"
                        . "port=5432;"
                        . "sslmode=require;"
                        . "password=f457e5a75151626f6a23493485673b3ea75595a7dcbcfd3b7994fc9d7f1e53d5";
                $db = new PDO($dbString); 
                
                if ($request["queryResult"]["action"] == "DBLink" ) {
                    $papercode = $request["queryResult"]["parameters"]["paper1"];
                    
                    $query = "SELECT * FROM papers WHERE papercode LIKE '%$papercode'";
                    $dbres = $db->query($query);

                    $pLevel = $pCode = $pName = $pPoints = $pCos = $pPres = "default";
                    while ($row = $dbres->fetch(PDO::FETCH_ASSOC)) {
                        $pLevel = $row["paperlevel"];
                        $pCode = $row["papercode"];
                        $pName = $row["papername"];
                        $pPoints = $row["paperpoints"];
                        $pCos = $row["papercos"];
                        $pPres = $row["paperpres"];
                    }

                    $dbres->closeCursor();

                    $presText = processPres($pPres);
                    
                    $text = $pName . " is a Level ". $pLevel . " Paper, that is worth " . $pPoints . " points. ".$presText ;

                } else if ($request["queryResult"]["action"] == "DBPaper" ) {
                    $papercode = $request["queryResult"]["parameters"]["paper1"];
                    
                    $query = "SELECT * FROM papers WHERE papername LIKE '%$papername'";
                    $dbres = $db->query($query);

                    $pLevel = $pCode = $pName = $pPoints = $pCos = $pPres = "default";
                    while ($row = $dbres->fetch(PDO::FETCH_ASSOC)) {
                        $pLevel = $row["paperlevel"];
                        $pCode = $row["papercode"];
                        $pName = $row["papername"];
                        $pPoints = $row["paperpoints"];
                        $pCos = $row["papercos"];
                        $pPres = $row["paperpres"];
                    }

                    $dbres->closeCursor();

                    $presText = processPres($pPres);
                    
                    $text = $pName . " is a Level ". $pLevel . " Paper, that is worth " . $pPoints . " points. ".$presText;

                } else if ($request["queryResult"]["action"] == "DBMajor") {
                    $major = $request["queryResult"]["parameters"]["Major1"];

                    switch ($major) {
                        case "Software Development" :
                            $dbMajor = "isSD";
                            break;
                        case "IT Service Science" :
                            $dbMajor = "isITSS";
                            break;
                        case "Computer Science" :
                            $dbMajor = "isCS";
                            break;
                        case "Analytics" :                                
                            $dbMajor = "isAL";
                            break;
                        case "Computational Intelligence" :                               
                            $dbMajor = "isCI";
                            break;
                        case "Networds and Security" :                               
                            $dbMajor = "isNS";
                            break;
                    }

                    $query = "SELECT * FROM papers WHERE ".$dbMajor." = true";
                    $dbres = $db->query($query);

                    $pName = array();
                    $pCode = array();

                    while ($row = $dbres->fetch(PDO::FETCH_ASSOC)) {
                        array_push($pName, $row["papercode"]);
                        array_push($pCode, $row["papername"]);
                    }
                    console.log($pName);
                    $dbres->closeCursor();

                    $text = "";
                    for($x = 0; $x < sizeof($pName); $x++) {
                        $text .= $pCode[$x] . " ";
                        $text .= $pName[$x] . " <br>";
                    }
                }



                $response = new \stdClass();
                $response->fulfillmentText = $text;
                $response->source = $update["queryResult"]["source"];
                sendMessage($response);

                

    }
        
    function sendMessage($parameters) {
        echo json_encode($parameters);
    }
    
    $request_response = file_get_contents("php://input");
    $request = json_decode($request_response, true);
    if (isset($request["queryResult"]["action"])) {
        processMessage($request);
    }
?>