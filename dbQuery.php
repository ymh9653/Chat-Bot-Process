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

    function processCos($string) {
        if ($string == "N/A") {
            return " and there are no Co-Requisite papers.";
        } else {
            return " and it has one Co-Requisite, ".$string.".";
        }
    }

    function processLecturer($string) {
        if ($string == "N/A") {
            return null;
        } else {
            return " It's taught by " . $string ."!";
        }
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
                        $pTutor = $row["papertutor"];
                    }

                    $dbres->closeCursor();

                    $presText = processPres($pPres);
                    $coText = processCos($pCos);
                    $lectText = processLecturer($pTutor);



                    $text = $pCode .": ". $pName . " is a Level ". $pLevel . " Paper, that is worth " . $pPoints . " points. "
                                        .$presText.$coText.$lectText;

                    


                } else if ($request["queryResult"]["action"] == "DBMajor") {
                    $major = $request["queryResult"]["parameters"]["Major1"];

                    switch ($major) {
                        case "Software development" :
                            $dbMajor = "software-development";
                            $text = "Do you love to code? Do you want to design and develop new software?"
                            ."Then you shouldn't look any further than majoring in Software Development!";
                            break;
                        case "IT service Science" :
                            $dbMajor = "it-service-science";
                            $text = "If you want to help the world by producing solutions for Information "
                            ."Technology, then IT Service Science just might be the place for you. You should "
                            ."give it a look and learn about analysing, designing and implementing solutions for everyone!";
                            break;
                        case "Computer Science" :
                            $dbMajor = "computer-science";
                            $text = "I've heard that the Computer Science major is pretty cool for solving problems "
                            ."and learning new technologies!";
                            break;
                        case "Analytics" :                                
                            $dbMajor = "analytics";
                            $text = "Analytics is essential for today's business environment and studying "
                            ."it will give you cool new skills in handling sophisticated predictive modelling "
                            ."and quantitative and statistical analysis!";
                            break;
                        case "Computational Intelligence" :                            
                            $dbMajor = "computational-intelligence";
                            $text = "Computational Intelligence is all about information and how we use it! "
                            ."If you have an interest for data, then I recommend you give this major a look!";
                            break;
                        case "Networks and Security" :                               
                            $dbMajor = "networks-and-security";
                            $text = "Are you intrigued by the concepts of Networking and Security? Then you "
                            ."should take part in helping build the network infrastructure and security levels "
                            ."of our world!";
                            break;
                    }

                    $text .= " Click here to learn more about it: https://www.aut.ac.nz/study/study-options/engineering-computer-and-mathematical-sciences/courses/"
                    ."bachelor-of-computer-and-information-sciences/".$dbMajor."-major";
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