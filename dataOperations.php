<?php
include 'dbOperations.php';

$dbOperator = new DBOperations();

session_start();

if (isset($_POST['isForLogIn'])) {
    // Sanitize inputs (basic example)
    $userName = trim($_POST['userName']);
    $password = trim($_POST['password']);

    // Build condition array
    $selectCondition = array(
        "USER_NAME" => $userName,
        "PASSWORD"  => $password  // In production, use hashed password!
    );

    // Assuming $dbOperator is a valid DB helper instance
    $jsonResult = $dbOperator->selectQueryToJson("branch_details", "*", $selectCondition);
    $dataArray = json_decode($jsonResult, true);

    if (!empty($dataArray)) {
        // Set session values
        $_SESSION['userId']   = $dataArray[0]['BRANCH_OFFICE_ID'];
        $_SESSION['userName'] = $dataArray[0]['USER_NAME'];
        $_SESSION['admin']    = $dataArray[0]['BRANCH_NAME'];
        $_SESSION['place']    = $dataArray[0]['PLACE'];

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Invalid credentials."]);
    }
}


if (isset($_POST['isForUpdatePassword'])) {
    $data = array(
        "PASSWORD"          => $_POST['newPassword']
    );
    $updateConditions = array(
        "USER_NAME"         => $_POST['userName']
    );
    echo $dbOperator->updateData("user_info", $data, $updateConditions);
}

if (isset($_POST['registerNewAgent'])) {
    $data = array(
        "AGENT_NAME"        => $_POST['agentName'],
        "MOBILE"            => $_POST['mobile'],
        "ALTERNATE_MOBILE"  => $_POST['alternateMobile'],
        "ADDRESS"           => $_POST['address'],
        "GST_NUMBER"        => $_POST['gstNum'],
        "USER_NAME"         => $_POST['userName'],
        "PASSWORD"          => $_POST['password']
    );
    echo $dbOperator->insertData("user_info", $data);
}
//getSaplePiDetails
if (isset($_POST['getBookingDetails'])) {
    if (empty($_POST['samplePiId'])) {
        echo json_encode(['status' => 'error', 'message' => 'No booking ID provided']);
        exit;
    }
    $selectCondition = array(
        "BOOKING_ID" => $_POST['samplePiId']
    );
    echo $dbOperator->selectQueryToJson("booking_details", "*", $selectCondition);
}


//Add place 
if (isset($_POST['addNewPlace'])) {
    $place = $_POST['place'];
    $selectCondition = array(
        "CITY_NAME" => $place
    );
    $jsonString = $dbOperator->selectQueryToJson("city", "CITY_NAME", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);

    if ($rowCount > 0) {
        echo "PLACE_ALREADY_EXISTS";
    } else {
        $data = array(
            "CITY_NAME"  => $place
        );
        echo $dbOperator->insertData("city", $data);
    }
}
// Delete place
if (isset($_POST['deletePlace'])) {
    $placeId = $_POST['place'];
    $conditions = array(
        "CITY_ID" => $placeId
    );
    echo $dbOperator->deleteRecord("city", $conditions);
}

// editPlace
if (isset($_POST['editPlace'])) {
    $placeId = $_POST['placeId'];
    $newPlaceName = $_POST['newPlaceName'];
    $data = array(
        "CITY_NAME" => $newPlaceName
    );
    $place = mysqli_real_escape_string($conn, $newPlaceName);
    $checkQuery = "SELECT CITY_NAME FROM city WHERE CITY_NAME = '$place'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "PLACE_ALREADY_EXISTS";
    } else {
        $conditions = array(
            "CITY_ID" => $placeId
        );
        echo $dbOperator->updateData("city", $data, $conditions);
    }
}


//addBranchDetails
if (isset($_POST['addBranchDetails'])) {
    $inputs = [
        'branchName',
        'branchMobile',
        'branchAlternativeMobile',
        'branchAddress',
        'branchPlace',
        'userName',
        'password',
        'commission',
        'bookingCommission',
        'recivedCommission',
        'isAgent'
    ];

    foreach ($inputs as $input) {
        $$input = mysqli_real_escape_string($conn, $_POST[$input] ?? '');
    }

    // Check for existing entries
    $uniqueChecks = [
        'BRANCH_NAME' => $branchPlace,
        'BRANCH_MOBILE' => $branchMobile,
        'USER_NAME' => $userName
    ];

    foreach ($uniqueChecks as $field => $value) {
        $json = $dbOperator->selectQueryToJson("branch_details", $field, [$field => $value]);
        if (count(json_decode($json, true)) > 0) {
            echo strtoupper($field) . "_ALREADY_EXISTS";
            return;
        }
    }


    $expenseDescriptions = $_POST['expense_description'] ?? [];
    $expenseAmounts = $_POST['expense_amount'] ?? [];
    $expenseValues = $_POST['expense'] ?? [];

    $expenses = [];

    for ($i = 0; $i < count($expenseDescriptions); $i++) {
        $desc = trim($expenseDescriptions[$i]);
        $amt = trim($expenseAmounts[$i]);

        if ($desc !== '' || $amt !== '') {
            $expenses[] = [
                'description' => mysqli_real_escape_string($conn, $desc),
                'amount' => mysqli_real_escape_string($conn, $amt)
            ];
        }
    }


    $totalExpenseAmount = 0;
    foreach ($expenseValues as $val) {
        $totalExpenseAmount += floatval($val);
    }

    $expenseJson = json_encode($expenses);


    $data = [
        "BRANCH_NAME" => $branchName,
        "BRANCH_MOBILE" => $branchMobile,
        "ALTERNATIVE_MOBILE" => $branchAlternativeMobile,
        "ADDRESS" => $branchAddress,
        "PLACE" => $branchPlace,
        "USER_NAME" => $userName,
        "PASSWORD" => $password,
        "BOOKING_COMMISSION"      => !empty($bookingCommission) ? $bookingCommission : null,
        "RECIVED_COMMISSION"    => !empty($recivedCommission) ? $recivedCommission : null,
        "EXPENSE"              => !empty($expenseArray) ? json_encode($expenseArray) : null,
        "TOTAL_EXPENSE_AMOUNT" => !empty($totalExpenseAmount) ? $totalExpenseAmount : null,
        "STATUS" => 0,
        "ISAGENT" => $isAgent ?: 0
    ];

    echo $dbOperator->insertData("branch_details", $data);
}

//updateBranchDetails
if (isset($_POST['updateBranchDetails'])) {
    $branchOfficeId = mysqli_real_escape_string($conn, $_POST['branchOfficeId']);
    $inputs = [
        'branchName',
        'branchMobile',
        'branchAlternativeMobile',
        'branchAddress',
        'branchPlace',
        'userName',
        'password',
        'commission',
        'paidCommission',
        'totalCommission    ',
        'isAgent'
    ];

    foreach ($inputs as $input) {
        $$input = mysqli_real_escape_string($conn, $_POST[$input] ?? '');
    }


    $expenseDescriptions = $_POST['expense_description'] ?? [];
    $expenseAmounts = $_POST['expense_amount'] ?? [];
    $expenseValues = $_POST['expense'] ?? [];

    $expenses = [];

    for ($i = 0; $i < count($expenseDescriptions); $i++) {
        $desc = trim($expenseDescriptions[$i]);
        $amt = trim($expenseAmounts[$i]);

        if ($desc !== '' || $amt !== '') {
            $expenses[] = [
                'description' => mysqli_real_escape_string($conn, $desc),
                'amount' => mysqli_real_escape_string($conn, $amt)
            ];
        }
    }


    $totalExpenseAmount = 0;
    foreach ($expenseValues as $val) {
        $totalExpenseAmount += floatval($val);
    }

    $expenseJson = json_encode($expenses);


    $data = [
        "BRANCH_NAME" => $branchName,
        "BRANCH_MOBILE" => $branchMobile,
        "ALTERNATIVE_MOBILE" => $branchAlternativeMobile,
        "ADDRESS" => $branchAddress,
        "PLACE" => $branchPlace,
        "USER_NAME" => $userName,
        "PASSWORD" => $password,
        "COMMISSION"           => !empty($commission) ? $commission : null,
        "PAID_COMMISSION"      => !empty($paidCommission) ? $paidCommission : null,
        "TOPAID_COMMISSION"    => !empty($totalCommission) ? $totalCommission : null,
        "EXPENSE"              => !empty($expenseArray) ? json_encode($expenseArray) : null,
        "TOTAL_EXPENSE_AMOUNT" => !empty($totalExpenseAmount) ? $totalExpenseAmount : null,
        "STATUS" => 0,
        "ISAGENT" => $isAgent ?: 0
    ];
    echo $dbOperator->updateData("branch_details", $data, ["BRANCH_OFFICE_ID" => $branchOfficeId]);
}

//deleteBranch
if (isset($_POST['deleteBranch'])) {
    $branchId = mysqli_real_escape_string($conn, $_POST['BranchId']);
    $conditions = array(
        "BRANCH_OFFICE_ID" => $branchId
    );
    echo $dbOperator->deleteRecord("branch_details", $conditions);
}


//addHub
if (isset($_POST['addNewHub'])) {
    $hubName = $_POST['hub'];
    $hubMobile = $_POST['hubMobile'];
    $hubAddress = $_POST['hubAddress'];
    $selectCondition = array(
        "HUB_NAME" => $hubName
    );
    $jsonString = $dbOperator->selectQueryToJson("hub", "HUB_NAME", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);
    if ($rowCount > 0) {
        echo "HUB_NAME_ALREADY_EXISTS";
    } else {
        $data = array(
            "HUB_NAME" => $hubName,
            "HUB_MOBILE" => $hubMobile,
            "HUB_ADDRESS" => $hubAddress,
            "STATUS" => 0
        );
        echo $dbOperator->insertData("hub", $data);
    }
}

//editHub
if (isset($_POST['editHub'])) {
    $hubId = $_POST['hubId'];
    $hubName = $_POST['hubName'];
    $hubMobile = $_POST['hubMobile'];
    $hubAddress = $_POST['hubAddress'];
    $data = array(
        "HUB_NAME" => $hubName,
        "HUB_MOBILE" => $hubMobile,
        "HUB_ADDRESS" => $hubAddress
    );
    echo $dbOperator->updateData("hub", $data, ["HUB_ID" => $hubId]);
}

//deleteHub
if (isset($_POST['deleteHub'])) {
    $hubId = mysqli_real_escape_string($conn, $_POST['hubId']);
    $conditions = array(
        "HUB_ID" => $hubId
    );
    echo $dbOperator->deleteRecord("hub", $conditions);
}

//aad Driver
if (isset($_POST['addDriver'])) {
    $driverName = $_POST['driverName'];
    $driverMobile = $_POST['driverMobile'];
    $driverLicense = $_POST['driverLicense'];
    $vehicleno = $_POST['vehicleno'];
    $description = $_POST['description'];
    $advance = $_POST['advance'];

    $selectCondition = array(
        "MOBILE" => $driverMobile
    );
    $jsonString = $dbOperator->selectQueryToJson("driver_details", "MOBILE", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);
    if ($rowCount > 0) {
        echo "MOBILE_NUMBER_ALREADY_EXISTS";
    } else {
        $data = array(
            "DRIVER_NAME" => $driverName,
            "MOBILE" => $driverMobile,
            "LICENSE" => $driverLicense,
            "VEHICLE_NUMBER" => $vehicleno,
            "VEHICLE_DESCRIPTION" => $description,
            "ADVANCE_AMOUNT" => $advance,

        );
        echo $dbOperator->insertData("driver_details", $data);
    }
}

// editDriver
if (isset($_POST['editdriver'])) {
    $driverid = $_POST['driverid'];
    $driverName = $_POST['driverName'];
    $driverMobile = $_POST['driverMobile'];
    $driverLicense = $_POST['driverLicense'];
    $vehicleno = $_POST['vehicleno'];
    $description = $_POST['description'];
    $advance = $_POST['advance'];

    $checkQuery = $dbOperator->selectQueryToJson("driver_details", "MOBILE", array("MOBILE" => $driverName));
    $resultArray = json_decode($checkQuery, true);
    $rowCount = count($resultArray);

    if ($rowCount > 0) {
        echo "MOBILE_ALREADY_EXISTS";
    } else {
        $data = array(
            "DRIVER_NAME" => $driverName,
            "MOBILE" => $driverMobile,
            "LICENSE" => $driverLicense,
            "VEHICLE_NUMBER" => $vehicleno,
            "VEHICLE_DESCRIPTION" => $description,
            "ADVANCE_AMOUNT" => $advance
        );
        echo $dbOperator->updateData("driver_details", $data, ["DRIVER_ID" => $driverid]);
    }
}
// deleteDriver
if (isset($_POST['deleteDriver'])) {
    $driverId = mysqli_real_escape_string($conn, $_POST['driverId']);
    $conditions = array(
        "DRIVER_ID" => $driverId
    );
    echo $dbOperator->deleteRecord("driver_details", $conditions);
}



//add customer

if (isset($_POST['addCustomer'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo "INVALID_MOBILE_NUMBER";
        exit;
    }
    $selectCondition = array("MOBILE" => $mobile);
    $jsonString = $dbOperator->selectQueryToJson("customer_details", "MOBILE", $selectCondition);
    $resultArray = json_decode($jsonString, true);
    if (count($resultArray) > 0) {
        echo "MOBILE_NUMBER_ALREADY_EXISTS";
    } else {
        $data = array(
            "CUSTOMER_NAME" => $name,
            "MOBILE" => $mobile,
        );
        echo $dbOperator->insertData("customer_details", $data);
    }
}

// edit customer
if (isset($_POST['editcustomer'])) {
    $customerid = $_POST['customerid'];
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];

    $data = array(
        "CUSTOMER_NAME" => $name,
        "MOBILE" => $mobile,
    );
    echo $dbOperator->updateData("customer_details", $data, ["CUSTOMER_ID" => $customerid]);
}

//delete customer
if (isset($_POST['deleteCustomer'])) {
    $customerId = mysqli_real_escape_string($conn, $_POST['customerId']);
    $conditions = array(
        "CUSTOMER_ID" => $customerId
    );
    echo $dbOperator->deleteRecord("customer_details", $conditions);
}

//Add Item 
if (isset($_POST['addItem'])) {
    $ItemName= $_POST['ItemName'];
    $selectCondition = array(
        "ITEM_NAME" => $ItemName
    );
    $jsonString = $dbOperator->selectQueryToJson("items", "ITEM_NAME", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);

    if ($rowCount > 0) {
        echo "Item_ALREADY_EXISTS";
    } else {
        $data = array(
            "ITEM_NAME"  => $ItemName
        );
        echo $dbOperator->insertData("items", $data);
    }
}


// edit Item
if (isset($_POST['editItem'])) {
    $ItemId = $_POST['ItemId'];
    $ItemName = $_POST['ItemName'];

    $checkQuery = "SELECT ITEM_NAME FROM items WHERE ITEM_NAME = ? AND ITEM_ID != ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "si", $name, $Itemid); // 's' = string, 'i' = integer
    mysqli_stmt_execute($stmt);
    $checkResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "ITEM_ALREADY_EXISTS";
    } else {
        $updateQuery = "UPDATE items SET ITEM_NAME = ? WHERE ITEM_ID = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "si", $ItemName, $ItemId); // two strings and one integer

        if (mysqli_stmt_execute($updateStmt)) {
            echo "UPDATE_SUCCESSFUL";
        } else {
            echo "ERROR_UPDATING_CUSTOMER: " . mysqli_error($conn);
        }

        mysqli_stmt_close($updateStmt);
    }

    mysqli_stmt_close($stmt);
}


//delete Item
if (isset($_POST['deleteItem'])) {
    $ItemId = mysqli_real_escape_string($conn, $_POST['ItemId']);
    $conditions = array(
        "ITEM_ID" => $ItemId
    );
    echo $dbOperator->deleteRecord("items", $conditions);
}


// //UpdateBranchAccount
// if (isset($_POST['UpdateBranchAccount'])) {

//     $branchAccountId = $_POST['BRANCH_ACCOUNT_ID'];
//     $bookingAmount = $_POST['BOOKING_AMOUNT'];
//     $receivedAmount = $_POST['RECEIVED_AMOUNT'];
//     $bookingPercentage = $_POST['BOOKING_PERCENTAGE'];
//     $receivedPercentage = $_POST['RECEIVED_PERCENTAGE'];
//     $commissionamount = $_POST['COMMISSION_AMOUNT'];
//     $payment_type = $_POST['PAYMENT_TYPE'];
//     $paid_amount = $_POST['PAID_AMOUNT'];
//     $notes = $_POST['NOTES'];

//     $data = array(
//         "BOOKING_AMOUNT" => $bookingAmount,
//         "RECEIVED_AMOUNT" => $receivedAmount,
//         "BOOKING_PERCENTAGE" => $bookingPercentage,
//         "RECEIVED_PERCENTAGE" => $receivedPercentage,
//         "COMMISSION_AMOUNT" => $commissionamount,
//         "PAYAMENT_TYPE" => $payment_type,
//         "PAID_AMOUNT" => $paid_amount,
//         "NOTES" => $notes,
//         "IS_REQUEST" => 1
//     );

    
//     echo $dbOperator->updateData("branch_account", $data, ["BRANCH_ACCOUNT_ID" => $branchAccountId]);
    
// }

//Approvel
if(isset($_POST['approvel'])){
    $branchAccountId =$_POST['branchAccountID'];

    $data = array(
        "IS_REQUEST" => 0
    );
    echo $dbOperator->updateData("branch_account", $data, ["BRANCH_ACCOUNT_ID" => $branchAccountId]);



}

//Cancel
if (isset($_POST['Calcel'])) {
    $id = $_POST['Id'];
    $reason = $_POST['Reason'];

    $data = array(
        'ACCOUNTS_CANCEL_NOTE' => $reason,
        "IS_REQUEST" => 0

    );
    echo $dbOperator->updateData("branch_account", $data, ["BRANCH_ACCOUNT_ID" => $id]);

}




if (isset($_POST['updateExistingMember'])) {
    $memberId = $_POST['updateExistingMember'];
    $memberName = $_POST['memberName'];
    $memberMobile = $_POST['memberMobile'];
    $memberAddress = $_POST['memberAddress'];

    $data = array(
        "MEMBER_NAME" => $memberName,
        "MEMBER_MOBILE" => $memberMobile,
        "MEMBER_ADDRESS" => $memberAddress
    );
    $conditions = array(
        "MEMBER_ID" => $memberId,
    );
    echo $dbOperator->updateData("members", $data, $conditions);
}


if (isset($_POST['updateExistingMember'])) {
    $memberId = $_POST['updateExistingMember'];
    $memberName = $_POST['memberName'];
    $memberMobile = $_POST['memberMobile'];
    $memberAddress = $_POST['memberAddress'];

    $data = array(
        "MEMBER_NAME" => $memberName,
        "MEMBER_MOBILE" => $memberMobile,
        "MEMBER_ADDRESS" => $memberAddress
    );
    $conditions = array(
        "MEMBER_ID" => $memberId,
    );
    echo $dbOperator->updateData("members", $data, $conditions);
}

if (isset($_POST['deleteMemberId'])) {
    $memberId = $_POST['deleteMemberId'];
    $conditions = array(
        "MEMBER_ID" => $memberId,
    );
    echo $dbOperator->deleteRecord("members", $conditions);
}

if (isset($_POST['saveNewChitMaster'])) {
    $chitAmount             = $_POST['chitAmount'];
    $noOfChits              = $_POST['noOfChits'];
    $chitAmountDetails      = $_POST['chitAmountDetails'];
    $agentId                = $_POST['agentId'];

    $data = array(
        "CHIT_NAME"                 => $chitAmount,
        "NO_OF_CHITS"               => $noOfChits,
        "CREATED_BY_AGENT"          => $agentId
    );
    $dbOperator->insertData("chit_details", $data);

    /* Get the CHIT_MASTER_ID for correspondig chit */
    $selectCondition = array(
        "CHIT_NAME"                       => $chitAmount,
        "CREATED_BY_AGENT"                => $agentId
    );
    $chitJson = $dbOperator->selectQueryToJson("chit_details", "*", $selectCondition);
    $dataArray = json_decode($chitJson, true);
    $chitId = $dataArray[0]['CHIT_MASTER_ID'];

    /* Insert Chit Amount Mappings */
    $chitAmountDetailsObj = json_decode($chitAmountDetails, true);
    // print_r($chitAmountDetailsObj);
    for ($i = 0; $i < count($chitAmountDetailsObj); $i++) {
        $data = array(
            "CHIT_MASTER_ID"           => $chitId,
            "MONTHLY_AMOUNT"    => $chitAmountDetailsObj[$i]["MONTHLY_AMOUNT"],
            "KASAR"             => $chitAmountDetailsObj[$i]["KASAR_AMOUNT"],
            "TOTAL_AMOUNT"      => $chitAmountDetailsObj[$i]["TOTAL_AMOUNT"]
        );
        $dbOperator->insertData("chit_master_amount_mappings", $data);
    }
}

if (isset($_POST['saveNewGroup'])) {
    $groupName              = $_POST['groupName'];
    $noOfMembers            = $_POST['noOfMembers'];
    $noOfChits              = $_POST['noOfChits'];
    $startDate              = $_POST['startDate'];
    $endDate                = $_POST['endDate'];
    $membersArrayObj        = $_POST['membersArrayObj'];
    $chitAmountDetails      = $_POST['chitAmountDetails'];
    $agentId                = $_POST['agentId'];

    $data = array(
        "GROUP_NAME"                => $groupName,
        "NO_OF_MEMBERS"             => $noOfMembers,
        "NO_OF_CHITS"               => $noOfChits,
        "START_DATE"                => $startDate,
        "END_DATE"                  => $endDate,
        "IS_ACTIVE"                 => 1,
        "CREATED_BY_AGENT"          => $agentId
    );
    $dbOperator->insertData("group_info", $data);

    /* Get the GROUP_ID for correspondig group */
    $selectCondition = array(
        "GROUP_NAME"                => $groupName,
        "START_DATE"                => $startDate
    );
    $groupJson = $dbOperator->selectQueryToJson("group_info", "*", $selectCondition);
    $dataArray = json_decode($groupJson, true);
    $groupId = $dataArray[0]['GROUP_ID'];

    $membersArrayObj = json_decode($membersArrayObj, true);

    /* Insert Chit Amount Mappings */
    $chitAmountDetailsObj = json_decode($chitAmountDetails, true);
    // print_r($chitAmountDetailsObj);
    for ($i = 0; $i < count($chitAmountDetailsObj); $i++) {
        $data = array(
            "GROUP_ID" => $groupId,
            "CHIT_MONTH" => $chitAmountDetailsObj[$i]["CHIT_MONTH"] . "-01",
            "MONTHLY_AMOUNT" => $chitAmountDetailsObj[$i]["MONTHLY_AMOUNT"],
            "KASAR" => $chitAmountDetailsObj[$i]["KASAR_AMOUNT"],
            "TOTAL_AMOUNT" => $chitAmountDetailsObj[$i]["TOTAL_AMOUNT"],
            "IS_ACTIVE" => 1,
        );
        $dbOperator->insertData("chit_amount_mappings", $data);

        for ($j = 0; $j < count($membersArrayObj); $j++) {
            $data = array(
                "GROUP_ID" => $groupId,
                "CHIT_MONTH" => $chitAmountDetailsObj[$i]["CHIT_MONTH"] . "-01",
                "MONTHLY_AMOUNT" => $chitAmountDetailsObj[$i]["MONTHLY_AMOUNT"],
                "MEMBER_ID" => $membersArrayObj[$j]["MEMBER_ID"]
            );
            $dbOperator->insertData("accounts_details", $data);
        }
    }

    /* Insert Member Details Mapping */
    for ($i = 0; $i < count($membersArrayObj); $i++) {
        $memberId = $membersArrayObj[$i]["MEMBER_ID"];
        $data = array(
            "GROUP_ID" => $groupId,
            "MEMBER_ID" => $memberId
        );
        $dbOperator->insertData("chit_member_mappings", $data);
    }
}

if (isset($_POST['viewChitMasterDetails'])) {
    $selectCondition = array(
        "CHIT_MASTER_ID" => $_POST['groupId']
    );
    $groupChitsAmountInfo = $dbOperator->selectQueryToJson("v_chit_master_details", "*", $selectCondition);

    $response = array(
        "CHIT_AMOUNT" => $groupChitsAmountInfo
    );

    print_r(json_encode($response));
}

if (isset($_POST['viewGroupAndMemberDetails'])) {
    $selectCondition = array(
        "GROUP_ID" => $_POST['groupId']
    );
    $groupChitsAmountInfo = $dbOperator->selectQueryToJson("v_chit_group_details", "*", $selectCondition);
    $groupMembersInfo = $dbOperator->selectQueryToJson("v_chit_group_members", "*", $selectCondition);

    $response = array(
        "CHIT_AMOUNT" => $groupChitsAmountInfo,
        "MEMBER_INFO" => $groupMembersInfo
    );

    print_r(json_encode($response));
}

if (isset($_POST['deleteGroupId'])) {
    $conditions = array(
        "GROUP_ID" => $_POST['deleteGroupId']
    );
    echo $dbOperator->deleteRecord("chit_amount_mappings", $conditions);
    echo $dbOperator->deleteRecord("chit_member_mappings", $conditions);
    echo $dbOperator->deleteRecord("group_info", $conditions);
}

if (isset($_POST['deleteChitMasterId'])) {
    $conditions = array(
        "CHIT_MASTER_ID" => $_POST['deleteChitMasterId']
    );
    echo $dbOperator->deleteRecord("chit_master_amount_mappings", $conditions);
    echo $dbOperator->deleteRecord("chit_details", $conditions);
}

if (isset($_POST['getChitDatesForSelectedGroup'])) {
    $conditions = array(
        "GROUP_ID" => $_POST['groupId']
    );
    print_r($dbOperator->selectQueryToJson("v_chit_accounts_details", "DISTINCT CHIT_MONTH", $conditions, "CHIT_MONTH ASC"));
}

if (isset($_POST['getChitDatesForSelectedMember'])) {
    $conditions = array(
        "MEMBER_ID" => $_POST['memberId']
    );
    print_r($dbOperator->selectQueryToJson("v_chit_accounts_details", "DISTINCT CHIT_MONTH", $conditions, "CHIT_MONTH ASC"));
}

if (isset($_POST['getChitPaymentDetailsForSelectedGroup'])) {
    $conditions = array(
        "GROUP_ID" => $_POST['groupId'],
        "CHIT_MONTH" => $_POST['month']
    );
    print_r($dbOperator->selectQueryToJson("v_chit_accounts_details", "*", $conditions, "MEMBER_NAME ASC"));
}

if (isset($_POST['getChitPaymentDetailsForSelectedMember'])) {
    $conditions = array(
        "MEMBER_ID" => $_POST['memberId'],
        "CHIT_MONTH" => $_POST['month']
    );
    print_r($dbOperator->selectQueryToJson("v_chit_accounts_details", "*", $conditions, "GROUP_NAME ASC"));
}

if (isset($_POST['getChitPaymentDetailsForOutstanding'])) {
    $conditions = array(
        'MEMBER_ID'     => array('operator' => '=', 'value' => $_POST['memberId']),
        'CHIT_MONTH'    => array('operator' => '<', 'value' => date("Y-m-d"))
    );
    print_r($dbOperator->selectQueryToJsonWithAdditionalWhereClause("v_chit_accounts_details", "*", $conditions));
}

if (isset($_POST['updateChitWisePayment'])) {
    $dataArray = json_decode($_POST['dataArray'], true);
    print_r($dataArray);

    for ($i = 0; $i < count($dataArray); $i++) {
        $data = array(
            "PAID_AMOUNT" => ($dataArray[$i]["ADVANCE_AMOUNT"] + $dataArray[$i]["PAID_AMOUNT"]),
            "BALANCE" => $dataArray[$i]["CURRENT_BALANCE"]
        );
        $updateConditions = array(
            "ACCOUNT_ID" => $dataArray[$i]["ACCOUNT_ID"]
        );
        echo $dbOperator->updateData("accounts_details", $data, $updateConditions);

        $data = array();
        $data = array(
            "ACCOUNT_ID" => $dataArray[$i]["ACCOUNT_ID"],
            "ADVANCE_AMOUNT" => $dataArray[$i]["ADVANCE_AMOUNT"],
            "EXISTING_BALANCE" => $dataArray[$i]["EXISTING_BALANCE"],
            "PAID_AMOUNT" => $dataArray[$i]["PAID_AMOUNT"],
            "NEW_BALANCE" => $dataArray[$i]["CURRENT_BALANCE"]
        );
        echo $dbOperator->insertData("transaction_details", $data);
    }
}

if (isset($_POST['getMemberDetailsForSelectedGroup'])) {
    $conditions = array(
        "GROUP_ID" => $_POST['groupId'],
        "IS_TAKEN" => 0
    );
    print_r($dbOperator->selectQueryToJson("v_chit_group_members", "MEMBER_ID, CONCAT(MEMBER_NAME, ' - ', MEMBER_MOBILE) AS MEMBER_NAME", $conditions));
}

if (isset($_POST['updateChitTaker'])) {
    $data = array(
        "MEMBER_TAKEN" => $_POST['memberId']
    );
    $updateConditions = array(
        "CHIT_ID" => $_POST['chitId'],
        "GROUP_ID" => $_POST['groupId'],
    );
    echo $dbOperator->updateData("chit_amount_mappings", $data, $updateConditions);

    $data = array();
    $data = array(
        "IS_TAKEN" => 1
    );
    $updateConditions = array();
    $updateConditions = array(
        "MEMBER_ID" => $_POST['memberId'],
        "GROUP_ID" => $_POST['groupId'],
        "IS_TAKEN" => 0,
    );
    echo $dbOperator->updateData("chit_member_mappings", $data, $updateConditions, " LIMIT 1");
}

if (isset($_POST['getChitAndMemberNamesForGivenDate'])) {
    $conditions = array(
        'USER_ID'       => array('operator' => '=', 'value' => $_POST['agentId']),
        'PAID_AMOUNT'   => array('operator' => '>', 'value' => '0'),
        'PAID_DATE'     => array('operator' => '>=', 'value' => $_POST['startDate']),
        'PAID_DATE'     => array('operator' => '<=', 'value' => $_POST['endDate'])
    );
    print_r(
        $dbOperator->selectQueryToJsonWithAdditionalWhereClause(
            "v_chit_transaction_details",
            "GROUP_NAME, CONCAT(MEMBER_NAME, ' - ', MEMBER_MOBILE) AS MEMBER_NAME",
            $conditions
        )
    );
}

if (isset($_POST['isForOutstandingReportBasedOnMember'])) {
    $conditions = array(
        'MEMBER_ID'     => array('operator' => '=', 'value' => $_POST['memberId']),
        'CHIT_MONTH'    => array('operator' => '=', 'value' => $_POST['chitMonth']),
        'GROUP_ID'      => array('operator' => '=', 'value' => $_POST['groupId']),
        'PAID_AMOUNT'   => array('operator' => '>', 'value' => 0)
    );

    $selectConditions = "GROUP_NAME, CHIT_MONTH, PAID_AMOUNT, NEW_BALANCE AS BALANCE, DATE_FORMAT(PAID_DATE, '%Y-%m-%d') AS PAID_DATE";
    print_r($dbOperator->selectQueryToJsonWithAdditionalWhereClause(
        "v_chit_transaction_details",
        $selectConditions,
        $conditions
    ));
}
