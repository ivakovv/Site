/*Триггер для обновления количества товара после создания заказа*/
CREATE OR REPLACE FUNCTION update_product_stock()
RETURNS TRIGGER AS $$
BEGIN
    -- Уменьшаем количество товара на складе после создания заказа
    UPDATE products 
    SET stock_quanity = stock_quanity - NEW.quantity
    WHERE product_id = NEW.product_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_order_insert
AFTER INSERT ON orders
FOR EACH ROW
EXECUTE FUNCTION update_product_stock();

/*Триггер для обновления времени последнего изменения товара*/
CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_product_update
BEFORE UPDATE ON products
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();

/*Триггер для проверки наличия товара перед заказом*/
CREATE OR REPLACE FUNCTION check_stock_availability()
RETURNS TRIGGER AS $$
BEGIN
    -- Проверяем достаточно ли товара на складе
    IF (SELECT stock_quanity FROM products WHERE product_id = NEW.product_id) < NEW.quantity THEN
        RAISE EXCEPTION 'Недостаточно товара на складе';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_order_insert
BEFORE INSERT ON orders
FOR EACH ROW
EXECUTE FUNCTION check_stock_availability();

/*Триггер для очистки корзины после создания заказа*/
CREATE OR REPLACE FUNCTION clear_cart_after_order()
RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM cart 
    WHERE user_id = NEW.user_id 
    AND product_id = NEW.product_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_order_created
AFTER INSERT ON orders
FOR EACH ROW
EXECUTE FUNCTION clear_cart_after_order();

/*Триггер для проверки корректности формата email*/
CREATE OR REPLACE FUNCTION validate_email()
RETURNS TRIGGER AS $$
BEGIN
    -- Проверяем формат email с помощью регулярного выражения
    IF NEW.email !~ '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$' THEN
        RAISE EXCEPTION 'Неверный формат email';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_user_insert_update
BEFORE INSERT OR UPDATE ON users
FOR EACH ROW
EXECUTE FUNCTION validate_email();