DROP PROCEDURE IF EXISTS CalculateBranchAccounts;
DELIMITER //

CREATE PROCEDURE CalculateBranchAccounts()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_month INT;
    DECLARE v_branch_id INT;
    DECLARE v_booking_amount DECIMAL(10,2);
    DECLARE v_received_amount DECIMAL(10,2);
    DECLARE v_paid_percentage DECIMAL(5,2);
    DECLARE v_topaid_percentage DECIMAL(5,2);
    DECLARE v_commission_amount DECIMAL(10,2);
    DECLARE v_admin_outstanding_amount DECIMAL(10,2);
    DECLARE v_is_agent INT;
    DECLARE v_total_expense DECIMAL(10,2);
    DECLARE v_paid_and_account_amount DECIMAL(10,2);
    DECLARE v_topay_amount DECIMAL(10,2);

    -- Cursor that combines FROM_PLACE_ID and TO_PLACE_ID month/branch combos
    DECLARE cur CURSOR FOR 
        SELECT DISTINCT 
            EXTRACT(MONTH FROM BOOKING_DATE) AS month,
            branch_id
        FROM (
            SELECT FROM_PLACE_ID AS branch_id, BOOKING_DATE FROM booking_details
            UNION ALL
            SELECT TO_PLACE_ID AS branch_id, BOOKING_DATE FROM booking_details
        ) AS combined;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO v_month, v_branch_id;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Booking amount (FROM branch)
        SELECT IFNULL(SUM(TOTAL_AMOUNT), 0) INTO v_booking_amount
        FROM booking_details
        WHERE FROM_PLACE_ID = v_branch_id
        AND EXTRACT(MONTH FROM BOOKING_DATE) = v_month;

        -- Received amount (TO branch)
        SELECT IFNULL(SUM(TOTAL_AMOUNT), 0) INTO v_received_amount
        FROM booking_details
        WHERE TO_PLACE_ID = v_branch_id
        AND EXTRACT(MONTH FROM BOOKING_DATE) = v_month;

        -- Get branch details
        SELECT ISAGENT, PAID_COMMISSION, TOPAID_COMMISSION, IFNULL(TOTAL_EXPENSE_AMOUNT, 0)
        INTO v_is_agent, v_paid_percentage, v_topaid_percentage, v_total_expense
        FROM branch_details
        WHERE BRANCH_OFFICE_ID = v_branch_id;

        -- Calculate commission
        IF v_is_agent = 1 THEN
            -- Agent commission
            SET v_commission_amount = 
                (v_booking_amount * v_paid_percentage / 100) +
                (v_received_amount * v_topaid_percentage / 100);
        ELSE
            -- Admin logic
            SET v_paid_percentage = 0;
            SET v_topaid_percentage = 0;
            SET v_commission_amount = v_booking_amount + v_received_amount;
        END IF;

        -- Calculate ADMIN_OUTSTANDING_AMOUNT:
        -- (PAID + ACCOUNT from FROM_PLACE_ID) + (TO_PAY from TO_PLACE_ID) - COMMISSION
        SELECT IFNULL(SUM(TOTAL_AMOUNT), 0) INTO v_paid_and_account_amount
        FROM booking_details
        WHERE FROM_PLACE_ID = v_branch_id
        AND EXTRACT(MONTH FROM BOOKING_DATE) = v_month
        AND PAYMENT_TYPE IN ('PAID', 'ACCOUNT');

        SELECT IFNULL(SUM(TOTAL_AMOUNT), 0) INTO v_topay_amount
        FROM booking_details
        WHERE TO_PLACE_ID = v_branch_id
        AND EXTRACT(MONTH FROM BOOKING_DATE) = v_month
        AND PAYMENT_TYPE = 'TO_PAY';

        SET v_admin_outstanding_amount = v_paid_and_account_amount + v_topay_amount - v_commission_amount;

        -- If admin branch, subtract expenses
        IF v_is_agent = 0 THEN
            SET v_admin_outstanding_amount = v_admin_outstanding_amount - v_total_expense;
        END IF;

        -- Insert or update branch account
        INSERT INTO branch_account (
            MONTH,
            BRANCH_ID,
            BOOKING_AMOUNT,
            RECEIVED_AMOUNT,
            BOOKING_PERCENTAGE,
            RECEIVED_PERCENTAGE,
            COMMISSION_AMOUNT,
            ADMIN_OUTSTANDING_AMOUNT,
            STATUS,
            IS_DELETE,
            CREATED_AT
        ) VALUES (
            v_month,
            v_branch_id,
            v_booking_amount,
            v_received_amount,
            v_paid_percentage,
            v_topaid_percentage,
            v_commission_amount,
            v_admin_outstanding_amount,
            0,
            0,
            NOW()
        )
        ON DUPLICATE KEY UPDATE
            BOOKING_AMOUNT = VALUES(BOOKING_AMOUNT),
            RECEIVED_AMOUNT = VALUES(RECEIVED_AMOUNT),
            BOOKING_PERCENTAGE = VALUES(BOOKING_PERCENTAGE),
            RECEIVED_PERCENTAGE = VALUES(RECEIVED_PERCENTAGE),
            COMMISSION_AMOUNT = VALUES(COMMISSION_AMOUNT),
            ADMIN_OUTSTANDING_AMOUNT = VALUES(ADMIN_OUTSTANDING_AMOUNT),
            STATUS = 0,
            IS_DELETE = 0,
            UPDATED_AT = NOW();
    END LOOP;

    CLOSE cur;
END //

DELIMITER ;
