<?php
session_start();
include 'dbConn.php';


include 'dbOperations.php';

$dbOperator = new DBOperations();


// date
date_default_timezone_set('Asia/Kolkata');
$date_1 =  date('d-m-Y H:i');
$date = date('Y-m-d', strtotime($date_1));
$dateTime = date('Y-m-d H:i', strtotime($date_1));


// Get Lr Number
function getLrNumber($conn) {
    $cdate = date('Y-m-d');
    $datePart = date('Ymd');

    // Check if LR number for today exists
    $checkDateQry = "SELECT LR_NUMBER FROM lr_number WHERE DATE = '$cdate' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkDateQry);

    if (!$checkResult) {
        throw new Exception("Failed to check LR number: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($checkResult) > 0) {
        // Fetch current LR and update it
        $row = mysqli_fetch_assoc($checkResult);
        $currentLR = (int)$row['LR_NUMBER'];
        $newLR = str_pad($currentLR + 1, 3, '0', STR_PAD_LEFT);

        $updateLRNumberQry = "UPDATE lr_number SET LR_NUMBER = '$newLR' WHERE DATE = '$cdate'";
        if (!mysqli_query($conn, $updateLRNumberQry)) {
            throw new Exception("Failed to update LR number: " . mysqli_error($conn));
        }

        $lrSerial = $newLR;
    } else {
        // Insert initial LR number for the day
        $lrSerial = '001';
        $insertLRQry = "INSERT INTO lr_number (DATE, LR_NUMBER, STATUS) VALUES ('$cdate', '$lrSerial', 0)";
        if (!mysqli_query($conn, $insertLRQry)) {
            throw new Exception("Failed to insert LR number: " . mysqli_error($conn));
        }
    }

    // Construct final LR number format
    $finalLRNumber = 'ZH-' . $datePart . '-' . $lrSerial;
    return $finalLRNumber;
}

//Insert Booking Details
if (isset($_POST['isNewBooking'])) {
    $cdate = date('Y-m-d');
    $datePart = date('Ymd');
    $customer = $_POST['customer'];
    $getCustomerIdQuery = "SELECT CUSTOMER_ID FROM customer_details WHERE CUSTOMER_NAME = '$customer'";
    $customerId = 0;
    $result = mysqli_query($conn, $getCustomerIdQuery);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $customerId = $row['CUSTOMER_ID'];
        }
    }
    $mobile = $_POST['mobile'];
    $deliveryTo = $_POST['deliveryTo'];
    $deliveryMobile = $_POST['deliveryMobile'];
    $fromPlace = $_POST['fromPlace'];

    $stmt = $conn->prepare("SELECT BRANCH_OFFICE_ID FROM branch_details WHERE BRANCH_NAME = ?");
    $stmt->bind_param("s", $fromPlace);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $fromPlaceID = $row['BRANCH_OFFICE_ID'];
    } else {
        $fromPlaceID = null;
    }
    
    
    $fromMobile = $_POST['fromMobile'];
    $toPlace = $_POST['toPlace'];

    $stmt = $conn->prepare("SELECT BRANCH_OFFICE_ID FROM branch_details WHERE BRANCH_NAME = ?");
    $stmt->bind_param("s", $toPlace);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $toPlaceID = $row['BRANCH_OFFICE_ID'];
    } else {
        $toPlaceID = null;
    }
    
    $toMobile = $_POST['toMobile'];
    $fromPlaceID = $fromPlaceID;
    $toPlaceID = $toPlaceID;
    $noOfQty = $_POST['noOfQty'];
    $itemsAndQuantityDetails = $_POST['itemsAndQuantityDetails'];
    $qtyDescription = empty($_POST['qtyDescription']) ? "" : $_POST['qtyDescription'];
    $paymentType = $_POST['paymentType'];
    $totalAmount = $_POST['totalAmount'];

    if ($paymentType == "ACCOUNT") {
        $checkAccountTypeQuery = "SELECT * FROM account_type WHERE CUSTOMER_ID = $customerId";
        $result = mysqli_query($conn, $checkAccountTypeQuery);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $outstandingAmount = $row['OUTSTANDING_AMOUNT'];
            $newOutstandingAmount = $outstandingAmount + $totalAmount;

            $updateQuery = "UPDATE account_type SET OUTSTANDING_AMOUNT = $newOutstandingAmount WHERE CUSTOMER_ID = $customerId";
            mysqli_query($conn, $updateQuery);
        } else {
            $insertQuery = "INSERT INTO account_type (CUSTOMER_ID, OUTSTANDING_AMOUNT, STATUS) 
                            VALUES ('$customerId', $totalAmount, 0)";
            mysqli_query($conn, $insertQuery);
        }
    }
    $transportationCharge = $_POST['transportationCharge'];
    $loadingCharge = empty($_POST['loadingCharge']) ? 0 : $_POST['loadingCharge'];
    $additionalCharge = empty($_POST['additionalCharge']) ? 0 : $_POST['loadingCharge'];
    $totalAmount = $_POST['totalAmount'];
    $goodsValue = $_POST['goodsValue'];
    $deliveryMethod = $_POST['deliveryMethod'];

    // $notes = empty($_POST['notes']) ? "" : $_POST['notes'];
    "goodsValue: " . $goodsValue;
    try {
        if (isset($conn)) {

            mysqli_begin_transaction($conn);

            $lrNumber = getLrNumber($conn);

            $bookingData = array(
                'LR_NUMBER' => $lrNumber,
                'CUSTOMER_ID' => $customerId,
                'CUSTOMER' => $customer,
                'MOBILE' => $mobile,
                'DELIVERY_TO' => $deliveryTo,
                'DELIVERY_MOBILE' => $deliveryMobile,
                'FROM_PLACE' => $fromPlace,
                'FROM_MOBILE' => $fromMobile,
                'TO_PLACE' => $toPlace,
                'TO_MOBILE' => $toMobile,
                'FROM_PLACE_ID'=>$fromPlaceID,
                'TO_PLACE_ID'=>$toPlaceID,
                'QUANTITY' => $noOfQty,
                'QUANTITY_DETAILS' => $itemsAndQuantityDetails,
                'QTY_DESCRIPTION' => $qtyDescription,
                'PAYMENT_TYPE' => $paymentType,
                'TOTAL_AMOUNT' => $totalAmount,
                'TRANSPORTATION_COST' => $transportationCharge,
                'LOADING_COST' => $loadingCharge,
                'ADDITIONAL_COST' => $additionalCharge,
                'GOODS_VALUE' => $goodsValue,
                'DELIVERY_TYPE' => $deliveryMethod,
                'BOOKING_STAUTS' => 0,
                'BOOKING_DATE' => $cdate,
                'LAST_UPDATE_DATE' => $cdate,
                'STATUS' => 0
            );


           echo $response = $dbOperator->insertData("booking_details", $bookingData);
            $responseParts = explode('-', $response);
            $bookingId = trim(end($responseParts));
            mysqli_commit($conn);
            print_r($bookingId);
            

            // $data = array(
            //     "BOOKING_ID" => $bookingId
            // );
            // $updateBookingID = $dbOperator->updateData('customer_details',$data,['CUSTOMER' => $customer]);
        }
    } catch (Exception $e) {

        if (isset($conn)) {
            mysqli_rollback($conn);
        }
        print_r("Error Occurred: " . $e->getMessage());
    }
}
//Edit Booking Details
if (isset($_POST['isEditBooking'])) {

    $bookingId = $_POST['bookingId'];
    $customer = $_POST['customer'];
    $getCustomerIdQuery = "SELECT CUSTOMER_ID FROM customer_details WHERE CUSTOMER_NAME = '$customer'";
    $customerId = 0;
    $result = mysqli_query($conn, $getCustomerIdQuery);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo $customerId = $row['CUSTOMER_ID'];
        }
    }
    $mobile = $_POST['mobile'];
    $deliveryTo = $_POST['deliveryTo'];
    $deliveryMobile = $_POST['deliveryMobile'];
    $fromPlace = $_POST['fromPlace'];
    $fromMobile = $_POST['fromMobile'];
    $toPlace = $_POST['toPlace'];
    $toMobile = $_POST['toMobile'];
    $noOfQty = $_POST['noOfQty'];
    $itemsAndQuantityDetails = $_POST['itemsAndQuantityDetails'];
    $qtyDescription = empty($_POST['qtyDescription']) ? "" : $_POST['qtyDescription'];
    $paymentType = $_POST['paymentType'];
    $totalAmount = $_POST['totalAmount'];

    if ($paymentType == "ACCOUNT") {
        $checkAccountTypeQuery = "SELECT * FROM account_type WHERE CUSTOMER_ID = $customerId";
        $result = mysqli_query($conn, $checkAccountTypeQuery);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $outstandingAmount = $row['OUTSTANDING_AMOUNT'];
            $newOutstandingAmount = $outstandingAmount + $totalAmount;

            $updateQuery = "UPDATE account_type SET OUTSTANDING_AMOUNT = $newOutstandingAmount WHERE CUSTOMER_ID = $customerId";
            mysqli_query($conn, $updateQuery);
        } else {
            $insertQuery = "INSERT INTO account_type (BOOKING_ID,CUSTOMER_ID, OUTSTANDING_AMOUNT, STATUS) 
                            VALUES ('$bookingId''$customerId', $totalAmount, 0)";
            mysqli_query($conn, $insertQuery);
        }
    }

    $transportationCharge = $_POST['transportationCharge'];
    $loadingCharge = empty($_POST['loadingCharge']) ? 0 : $_POST['loadingCharge'];
    $additionalCharge = empty($_POST['additionalCharge']) ? 0 : $_POST['loadingCharge'];
    $totalAmount = $_POST['totalAmount'];
    $goodsValue = $_POST['goodsValue'];
    $deliveryMethod = $_POST['deliveryMethod'];

    $data = array(
        'CUSTOMER_ID' => $customerId,
        'CUSTOMER' => $customer,
        'MOBILE' => $mobile,
        'DELIVERY_TO' => $deliveryTo,
        'DELIVERY_MOBILE' => $deliveryMobile,
        'FROM_PLACE' => $fromPlace,
        'FROM_MOBILE' => $fromMobile,
        'TO_PLACE' => $toPlace,
        'TO_MOBILE' => $toMobile,
        'QUANTITY' => $noOfQty,
        'QUANTITY_DETAILS' => $itemsAndQuantityDetails,
        'QTY_DESCRIPTION' => $qtyDescription,
        'PAYMENT_TYPE' => $paymentType,
        'TOTAL_AMOUNT' => $totalAmount,
        'TRANSPORTATION_COST' => $transportationCharge,
        'LOADING_COST' => $loadingCharge,
        'ADDITIONAL_COST' => $additionalCharge,
        'GOODS_VALUE' => $goodsValue,
        'DELIVERY_TYPE' => $deliveryMethod,
    );
    $where = array(
        'BOOKING_ID' => $bookingId
    );
    echo $dbOperator->updateData("booking_details", $data, $where);
}

if (isset($_POST['forBookingList'])) {
    $bookingId = $_POST['bookingId'];
    $selectSql = "SELECT * FROM booking_details WHERE BOOKING_ID = $bookingId";
    $bookingDetails = array();
    if (isset($conn) && $result = mysqli_query($conn, $selectSql)) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $bookingDetails['BOOKING_ID'] = $row['BOOKING_ID'];
                $bookingDetails['CUSTOMER'] = $row['CUSTOMER'];
                $bookingDetails['MOBILE'] = $row['MOBILE'];
                $bookingDetails['DELIVERY_TO'] = $row['DELIVERY_TO'];
                $bookingDetails['DELIVERY_MOBILE'] = $row['DELIVERY_MOBILE'];
                $bookingDetails['FROM_PLACE'] = $row['FROM_PLACE'];
                $bookingDetails['FROM_MOBILE'] = $row['FROM_MOBILE'];
                $bookingDetails['TO_PLACE'] = $row['TO_PLACE'];
                $bookingDetails['TO_MOBILE'] = $row['TO_MOBILE'];
                $bookingDetails['QUANTITY'] = $row['QUANTITY'];
                $bookingDetails['QUANTITY_DETAILS'] = $row['QUANTITY_DETAILS'];
                $bookingDetails['QTY_DESCRIPTION'] = $row['QTY_DESCRIPTION'];
                $bookingDetails['PAYMENT_TYPE'] = $row['PAYMENT_TYPE'];
                $bookingDetails['TOTAL_AMOUNT'] = $row['TOTAL_AMOUNT'];
                $bookingDetails['TRANSPORTATION_COST'] = $row['TRANSPORTATION_COST'];
                $bookingDetails['LOADING_COST'] = $row['LOADING_COST'];
                $bookingDetails['ADDITIONAL_COST'] = $row['ADDITIONAL_COST'];
                $bookingDetails['GOODS_VALUE'] = $row['GOODS_VALUE'];
                $bookingDetails['DELIVERY_TYPE'] = $row['DELIVERY_TYPE'];
                $bookingDetails['LR_NUMBER'] = $row['LR_NUMBER'];
                $bookingDetails['BOOKING_STAUTS'] = $row['BOOKING_STAUTS'];
            }
        }
    }
    print_r(json_encode($bookingDetails));
}

if (isset($_POST['moveToShipOutward'])) {
    $bookingId = $_POST['bookingId'];
    $driverDetails = $_POST['driverDetails'];
    $shipmentVia = $_POST['shipmentVia'];
    $bookingStatus = $shipmentVia == "Via_Coimbatore" ? 1 : 2;
    $showInShipOutward = $shipmentVia == "Via_Coimbatore" ? 1 : 0;

    $driverDetailsJson = json_decode($driverDetails, true);

    try {
        echo $updateQuery = "UPDATE booking_details SET 
                                BOOKING_STAUTS = $bookingStatus, 
                                LAST_UPDATE_DATE = '$date',
                                SHIPMENT_VIA = '$shipmentVia',
                                SHOW_IN_VIEW_SHIPOUTWARD = $showInShipOutward
                             WHERE BOOKING_ID = $bookingId";
        if (isset($conn)) {
            mysqli_query($conn, $updateQuery);

            echo $updateDriverDetailsQry = "
                        INSERT INTO shipment_details 
                            (BOOKING_ID, SHIPMENT_1_DATE, SHIPMENT_1_DATE_TIME, DRIVER_1_DETAILS)
                        VALUES
                            ($bookingId, '$date', '$dateTime', '$driverDetails')
                        ";

            mysqli_query($conn, $updateDriverDetailsQry);
            print_r("Success");

            $driverName = $driverDetailsJson['DRIVER_NAME'];
            $driverMobile = $driverDetailsJson['DRIVER_MOBILE'];
            $updateDriverDetailsQuery = "INSERT INTO driver_details (DRIVER_NAME, MOBILE)
                                            SELECT * FROM (SELECT '$driverName', '$driverMobile') AS tmp
                                            WHERE NOT EXISTS (
                                                SELECT DRIVER_NAME, MOBILE FROM driver_details WHERE DRIVER_NAME = '$driverName' AND MOBILE = '$driverMobile'
                                            ) LIMIT 1
                                        ";
            mysqli_query($conn, $updateDriverDetailsQuery);
        }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}
// if (isset($_POST['moveToShipOutward'])) {
//     $bookingId = $_POST['bookingId'];
//     $driverDetails = $_POST['driverDetails'];
//     $shipmentVia = $_POST['shipmentVia'];
//     $bookingStatus = $shipmentVia == "Via_Coimbatore" ? 1 : 2;
//     $showInShipOutward = $shipmentVia == "Via_Coimbatore" ? 1 : 0;
//     $date = date('Y-m-d');
//     $dateTime = date('Y-m-d H:i:s');

//     $driverDetailsJson = json_decode($driverDetails, true);

//     try {
//         $data = array(
//             'BOOKING_STATUS' => $bookingStatus,
//             'LAST_UPDATE_DATE' => $date,
//             'SHIPMENT_VIA' => $shipmentVia,
//             'SHOW_IN_VIEW_SHIPOUTWARD' => $showInShipOutward
//         );
//         $where = array('BOOKING_ID' => $bookingId);
//         echo $dbOperator->updateData("booking_details", $data, $where);

//         $shipmentInsertData = array(
//             'BOOKING_ID' => $bookingId,
//             'SHIPMENT_1_DATE' => $date,
//             'SHIPMENT_1_DATE_TIME' => $dateTime,
//             'DRIVER_1_DETAILS' => $driverDetails
//         );
//         echo $dbOperator->insertData("shipment_details", $shipmentInsertData);

//         $driverName = $driverDetailsJson['DRIVER_NAME'];
//         $driverMobile = $driverDetailsJson['DRIVER_MOBILE'];

//         $updateDriverDetailsQuery = "
//             INSERT INTO driver_details (DRIVER_NAME, MOBILE)
//             SELECT * FROM (SELECT '$driverName', '$driverMobile') AS tmp
//             WHERE NOT EXISTS (
//                 SELECT DRIVER_NAME, MOBILE FROM driver_details WHERE DRIVER_NAME = '$driverName' AND MOBILE = '$driverMobile'
//             ) LIMIT 1
//         ";
//         mysqli_query($conn, $updateDriverDetailsQuery);

//         echo "Success";
//     } catch (Exception $e) {
//         error_log("Error: " . $e->getMessage());
//         echo "Error occurred";
//     }
// }

if (isset($_POST['revertShipOutward'])) {
    $bookingId = $_POST['bookingId'];

    try {
        /* Update the booking_status in booking_details */
        $data = array(
            'BOOKING_STAUTS' => 0,
        );
        $where = array('BOOKING_ID' => $bookingId);
        echo $dbOperator->updateData("booking_details", $data, $where);

        // echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 0 WHERE BOOKING_ID = $bookingId";
        // if (isset($conn)) {
        //     mysqli_query($conn, $updateQuery);
        //     print_r("Success");
        // }
        /* Delete the existing records in shipment_details */


        echo $updateQuery = $dbOperator->deleteRecord("shipment_details", ["BOOKING_ID" => $bookingId]);

        // echo $updateQuery = "DELETE FROM shipment_details WHERE BOOKING_ID = $bookingId";
        // if (isset($conn)) {
        //     mysqli_query($conn, $updateQuery);
        //     print_r("Success");
        // }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['moveToCBEShipOutward'])) {
    $bookingId = $_POST['bookingId'];
    $driverDetails = $_POST['driverDetails'];
    try {

        $data = array(
            'BOOKING_STAUTS' => 2,
            'LAST_UPDATE_DATE' => $date,

        );
        $where = array('BOOKING_ID' => $bookingId);
        echo $dbOperator->updateData("booking_details", $data, $where);

        // echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 2, LAST_UPDATE_DATE = '$date' WHERE BOOKING_ID = $bookingId";
        // if (isset($conn)) {
        //     mysqli_query($conn, $updateQuery);


        //     // $data = array(
        //     //     'BOOKING_ID' => $bookingId,
        //     //     'SHIPMENT_2_DATE' => $date,
        //     //     'SHIPMENT_2_DATE_TIME' => $dateTime,
        //     //     'DRIVER_2_DETAILS' => $driverDetails
        //     // );
        //     // $where = array('BOOKING_ID' => $bookingId);
        //     // echo $updateDriverDetailsQry = $dbOperator->updateData("shipment_details", $data, $where);

        //     echo $updateDriverDetailsQry = "
        //                 UPDATE shipment_details SET 
        //                     SHIPMENT_2_DATE = '$date', 
        //                     SHIPMENT_2_DATE_TIME = '$dateTime', 
        //                     DRIVER_2_DETAILS = '$driverDetails'
        //                 WHERE BOOKING_ID = $bookingId
        //                 ";

        //     mysqli_query($conn, $updateDriverDetailsQry);
        //     print_r("Success");
        // }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['revertCBEShipOutward'])) {
    $bookingId = $_POST['bookingId'];
    try {

        $data = array(
            'BOOKING_STAUTS' => 1,
            'LAST_UPDATE_DATE' => $date,
        );
        $where = array('BOOKING_ID' => $bookingId);
        echo $dbOperator->updateData("booking_details", $data, $where);

        // echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 1 WHERE BOOKING_ID = $bookingId";
        // if (isset($conn)) {
        //     mysqli_query($conn, $updateQuery);
        //     // echo $deleteDriverDetailsQry = "DELETE FROM `shipoutward_details` WHERE `BOOKING_ID` = $bookingId";
        //     // mysqli_query($conn, $deleteDriverDetailsQry);
        //     print_r("Success");
        // }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['moveToShipInward'])) {
    $bookingId = $_POST['bookingId'];
    try {
        // $data = array(
        //     'BOOKING_STAUTS' => 3,
        //     'LAST_UPDATE_DATE' => $date,
        // );
        // $where = array('BOOKING_ID' => $bookingId);
        // echo $updateQuery = $dbOperator->updateData("booking_details", $data, $where);

        echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 3, LAST_UPDATE_DATE = '$date' WHERE BOOKING_ID = $bookingId";
        if (isset($conn)) {
            mysqli_query($conn, $updateQuery);
            print_r("Success");
        }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['revertShipinward'])) {
    $bookingId = $_POST['bookingId'];
    $shipmentVia = $_POST['shipmentVia'];
    try {
        if ($shipmentVia == "Via_Coimbatore") {

            // $data = array(
            //     'BOOKING_STAUTS' => 2,
            //     'LAST_UPDATE_DATE' => $date,
            // );
            // $where = array('BOOKING_ID' => $bookingId);
            // echo $updateQuery = $dbOperator->updateData("booking_details", $data, $where);

            echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 1 WHERE BOOKING_ID = $bookingId";
            if (isset($conn)) {
                mysqli_query($conn, $updateQuery);
                print_r("Success");
            }
        } else {

            // $data = array(
            //             'BOOKING_STAUTS' => 0,
            //             'LAST_UPDATE_DATE' => $date,
            //         );
            //         $where = array('BOOKING_ID' => $bookingId);
            //         echo $updateQuery = $dbOperator->updateData("booking_details", $data, $where);

            echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 0 WHERE BOOKING_ID = $bookingId";
            if (isset($conn)) {
                mysqli_query($conn, $updateQuery);
                print_r("Success");
            }
        }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['newCustomerName'])) {
    $mobileNumber = $_POST['mobileNumber'];
    $newCustomerName = $_POST['newCustomerName'];
    //    $insertQuery = "INSERT INTO customer_details (CUSTOMER_NAME) VALUES ('$newCustomerName')";
    /*$insertQuery = "INSERT INTO customer_details (CUSTOMER_NAME)
                    SELECT * FROM (SELECT '$newCustomerName') AS tmp
                    WHERE NOT EXISTS (
                        SELECT CUSTOMER_NAME FROM customer_details WHERE CUSTOMER_NAME = '$newCustomerName'
                    ) LIMIT 1";*/


    // $data = array(
    //     'CUSTOMER_NAME' => $newCustomerName,
    //     'MOBILE' => $mobileNumber
    // );
    // $where = array(
    //     'MOBILE' => $mobileNumber
    // );
    // $dbOperator->insertData("customer_details", $data, $where);


    $updateQuery = "
                        UPDATE customer_details SET
                          CUSTOMER_NAME = '$newCustomerName'
                        WHERE MOBILE = '$mobileNumber'
                   ";
    mysqli_query($conn, $updateQuery);
    echo "inserted";
}

if (isset($_POST['updateMobileNumber'])) {
    //    $customerName = $_POST['customerName'];
    $mobileNumber = $_POST['mobileNumber'];

    $data = array(
        'MOBILE' => $mobileNumber,
    );
    $where = array(
        'MOBILE' => $mobileNumber
    );
    $dbOperator->insertData("customer_details", $data, $where);

    $upsertQuery = "
                        INSERT INTO customer_details (MOBILE)
                        SELECT * FROM (SELECT '$mobileNumber') AS tmp
                        WHERE NOT EXISTS (
                            SELECT MOBILE FROM customer_details WHERE MOBILE = '$mobileNumber'
                        ) LIMIT 1
                   ";
    mysqli_query($conn, $upsertQuery);
}

if (isset($_POST['getBookingDetailsUnderCBEHub'])) {
    $toPlace = $_POST['toPlace'];


    // $data = array(
    //     'TO_PLACE' => $toPlace,
    //     'BOOKING_STATUS' => 1
    // );
    // $where = array(
    //     'TO_PLACE' => $toPlace,
    //     'BOOKING_STATUS' => 1
    // );
    // $dbOperator->selectQueryToJson("booking_details", $data, $where);

    $selectSql = "
                    SELECT *
                    WHERE TO_PLACE = '$toPlace' AND BOOKING_STATUS = 1
                ";
    $bookingDetails = array();
    if (isset($conn) && $result = mysqli_query($conn, $selectSql)) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $bookingDetails['BOOKING_ID'] = $row['BOOKING_ID'];
                $bookingDetails['CUSTOMER'] = $row['CUSTOMER'];
                $bookingDetails['MOBILE'] = $row['MOBILE'];
                $bookingDetails['DELIVERY_TO'] = $row['DELIVERY_TO'];
                $bookingDetails['DELIVERY_MOBILE'] = $row['DELIVERY_MOBILE'];
                $bookingDetails['FROM_PLACE'] = $row['FROM_PLACE'];
                $bookingDetails['FROM_MOBILE'] = $row['FROM_MOBILE'];
                $bookingDetails['TO_PLACE'] = $row['TO_PLACE'];
                $bookingDetails['TO_MOBILE'] = $row['TO_MOBILE'];
                $bookingDetails['QUANTITY'] = $row['QUANTITY'];
                $bookingDetails['QUANTITY_DETAILS'] = $row['QUANTITY_DETAILS'];
                $bookingDetails['QTY_DESCRIPTION'] = $row['QTY_DESCRIPTION'];
                $bookingDetails['PAYMENT_TYPE'] = $row['PAYMENT_TYPE'];
                $bookingDetails['TOTAL_AMOUNT'] = $row['TOTAL_AMOUNT'];
                $bookingDetails['TRANSPORTATION_COST'] = $row['TRANSPORTATION_COST'];
                $bookingDetails['LOADING_COST'] = $row['LOADING_COST'];
                $bookingDetails['ADDITIONAL_COST'] = $row['ADDITIONAL_COST'];
                $bookingDetails['GOODS_VALUE'] = $row['GOODS_VALUE'];
                $bookingDetails['DELIVERY_TYPE'] = $row['DELIVERY_TYPE'];
                $bookingDetails['INVOICE_NUMBER'] = $row['INVOICE_NUMBER'];
                $bookingDetails['BOOKING_STAUTS'] = $row['BOOKING_STAUTS'];
            }
        }
    }
    print_r(json_encode($bookingDetails));
}

//Delete Booking Details
if (isset($_POST['isDeleteBooking'])) {
    $bookingId = $_POST['cancelBookingId'];
    $deleteReason = $_POST['cancelReason'];
    $remarks = $_POST['remark'];
    $data = array(
        'IS_DELETE' => 1,
        'SHIPMENT_VIA' => null,
        'SHOW_IN_VIEW_SHIPOUTWARD' => null,
        'DELETE_REASON' => $deleteReason,
        'REMARK' => $remarks,
    );
    echo $dbOperator->updateData("booking_details", $data, ["BOOKING_ID" => $bookingId]);
}
