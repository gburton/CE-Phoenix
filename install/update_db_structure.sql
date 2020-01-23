/* Update to existing installations adjusting VARCHARs to 255 in length */
ALTER TABLE address_format 
CHANGE COLUMN address_format address_format VARCHAR(255) NOT NULL, 
CHANGE COLUMN address_summary address_summary VARCHAR(255) NOT NULL;


ALTER TABLE administrators
CHANGE COLUMN user_password user_password VARCHAR(255) NOT NULL;

ALTER TABLE categories
CHANGE COLUMN categories_image categories_image VARCHAR(255) NOT NULL;

ALTER TABLE categories_description
CHANGE COLUMN categories_name categories_name  VARCHAR(255) NOT NULL,
CHANGE COLUMN categories_seo_keywords categories_seo_keywords VARCHAR(255) NOT NULL,
CHANGE COLUMN categories_seo_title categories_seo_title  VARCHAR(255) NOT NULL;

ALTER TABLE configuration_group
CHANGE COLUMN configuration_group_title configuration_group_title  VARCHAR(255) NOT NULL;

ALTER TABLE currencies
CHANGE COLUMN title title VARCHAR(255) NOT NULL,
CHANGE COLUMN symbol_left symbol_left VARCHAR(255),
CHANGE COLUMN symbol_right symbol_right VARCHAR(255) NOT NULL;

ALTER TABLE customers
CHANGE COLUMN customers_password customers_password VARCHAR(255) NOT NULL;

ALTER TABLE hooks
CHANGE COLUMN hooks_site hooks_site VARCHAR(150) NOT NULL,
CHANGE COLUMN hooks_group hooks_group VARCHAR(150) NOT NULL,
CHANGE COLUMN hooks_code hooks_code VARCHAR(255) NOT NULL;

ALTER TABLE languages
CHANGE COLUMN name name VARCHAR(255) NOT NULL,
CHANGE COLUMN image image VARCHAR(255),
CHANGE COLUMN DIRECTORY DIRECTORY VARCHAR(255);

ALTER TABLE manufacturers
CHANGE COLUMN manufacturers_name manufacturers_name VARCHAR(255) NOT NULL,
CHANGE COLUMN manufacturers_image manufacturers_image VARCHAR(255);

ALTER TABLE manufacturers_info
CHANGE COLUMN manufacturers_seo_keywords manufacturers_seo_keywords VARCHAR(255) NOT NULL,
CHANGE COLUMN manufacturers_seo_title manufacturers_seo_title VARCHAR(255);

ALTER TABLE orders_products
CHANGE COLUMN products_model products_model VARCHAR(255),
CHANGE COLUMN products_name products_name VARCHAR(255) NOT NULL;

ALTER TABLE orders_status
CHANGE COLUMN orders_status_name orders_status_name VARCHAR(255) NOT NULL;

ALTER TABLE orders_products_attributes
CHANGE COLUMN products_options products_options VARCHAR(255) NOT NULL,
CHANGE COLUMN products_options_values products_options_values VARCHAR(255) NOT NULL;

ALTER TABLE products
CHANGE COLUMN products_model products_model VARCHAR(255),
CHANGE COLUMN products_image products_image VARCHAR(255),
CHANGE COLUMN products_gtin products_gtin VARCHAR(255);

ALTER TABLE products_description
CHANGE COLUMN products_name products_name VARCHAR(255) NOT NULL DEFAULT '',
CHANGE COLUMN products_seo_keywords products_seo_keywords VARCHAR(255) NULL ,
CHANGE COLUMN products_seo_title products_seo_title VARCHAR(255) NULL ;

ALTER TABLE products_images
CHANGE COLUMN image image VARCHAR(255);

ALTER TABLE products_options
CHANGE COLUMN products_options_name products_options_name VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE products_options_values
CHANGE COLUMN products_options_values_name products_options_values_name VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE sessions
CHANGE COLUMN sesskey sesskey VARCHAR(255) NOT NULL;

ALTER TABLE tax_class
CHANGE COLUMN tax_class_title tax_class_title VARCHAR(255) NOT NULL;

ALTER TABLE geo_zones
CHANGE COLUMN geo_zone_name geo_zone_name VARCHAR(255) NOT NULL;

ALTER TABLE whos_online
CHANGE COLUMN session_id session_id VARCHAR(255) NOT NULL,
CHANGE COLUMN time_last_click time_last_click  VARCHAR(255) NOT NULL;

ALTER TABLE zones
CHANGE COLUMN zone_code zone_code VARCHAR(255) NOT NULL;

