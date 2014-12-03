ALTER TABLE orders DROP customer_service_id;
ALTER TABLE orders DROP shipping_module;
DELETE FROM configuration_group WHERE configuration_group_id = "72" AND configuration_group_title = "Order Editor";
DELETE FROM configuration WHERE configuration_key = "ORDER_EDITOR_PAYMENT_DROPDOWN";
DELETE FROM configuration WHERE configuration_key = "ORDER_EDITOR_USE_SPPC";
DELETE FROM configuration WHERE configuration_key = "ORDER_EDITOR_USE_QTPRO";
DELETE FROM configuration WHERE configuration_key = "ORDER_EDITOR_USE_AJAX";
DELETE FROM configuration WHERE configuration_key = "ORDER_EDITOR_CREDIT_CARD";
