<?php

class database{

    function opencon() {
        return new PDO(
            'mysql:host=localhost; dbname=inventory_db',
            username: 'root',
            password: ''
        );
    }

    function signupUser($firstname, $lastname, $username, $email, $password, $role = 'customer', $created_at = null) {
        $con = $this->opencon();
        if ($created_at === null) {
            $created_at = date('Y-m-d H:i:s');
        }
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO users (first_name, last_name, username, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $username, $email, $password, $role, $created_at]);
            $userID = $con->lastInsertId();
            $con->commit();
            return $userID;
        } catch (PDOException $e) {
            $con->rollBack();
            error_log("Signup Error: " . $e->getMessage());
            return false;
        }
    }

    function loginUser($username, $password) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    function getUserById($user_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function updateUser($firstname, $lastname, $username, $email, $role, $user_id) {
        try {
            $con = $this->opencon();
            $con->beginTransaction();
            $stmt = $con->prepare("UPDATE users SET first_name=?, last_name=?, username=?, email=?, role=? WHERE user_id=?");
            $stmt->execute([$firstname, $lastname, $username, $email, $role, $user_id]);
            $con->commit();
            return true;
        } catch(PDOException $e){
            $con->rollBack();
            return false;
        }
    }

    function deleteUser($user_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("DELETE FROM users WHERE user_id=?");
        return $stmt->execute([$user_id]);
    }

    function isUsernameExists($username) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    function isEmailExists($email) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllUsers() {
        $con = $this->opencon();
        $stmt = $con->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------ CATEGORY ------------------

    public function getAllCategories() {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM Category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------ PRODUCTS ------------------

    public function addProduct($name, $category_id, $price, $stock) {
        $con = $this->opencon();
        $stmt = $con->prepare("INSERT INTO Products (product_name, category_id, product_price, product_stock) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $category_id, $price, $stock]);
    }

    public function updateProduct($product_name, $product_price, $product_stock, $category_id, $products_id) {
        $con = $this->opencon();
        $con->beginTransaction();
        $stmt = $con->prepare("UPDATE Products SET product_name=?, product_price=?, product_stock=?, category_id=? WHERE products_id=?");
        $stmt->execute([$product_name, $product_price, $product_stock, $category_id, $products_id]);
        $con->commit();
        return true;
    }

    public function getProductById($products_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM products WHERE products_id = ?");
        $stmt->execute([$products_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllProducts() {
    $con = $this->opencon();
    $stmt = $con->query("SELECT * FROM products");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function getAvailableProducts() {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM products WHERE product_stock > 0");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredProducts($categoryId = 0, $search = '') {
        $con = $this->opencon();
        $query = "SELECT * FROM Products WHERE 1=1";
        $params = [];
        if ($categoryId > 0) {
            $query .= " AND category_id = ?";
            $params[] = $categoryId;
        }
        if (!empty($search)) {
            $query .= " AND product_name LIKE ?";
            $params[] = '%' . $search . '%';
        }
        $stmt = $con->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteProductById($products_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("DELETE FROM Products WHERE products_id = ?");
        return $stmt->execute([$products_id]);
    }

    public function getLowStockProducts($categoryId = 0) {
        $con = $this->opencon();
        $query = "SELECT product_name, product_stock FROM Products WHERE product_stock <= 5";
        $params = [];
        if ($categoryId > 0) {
            $query .= " AND category_id = ?";
            $params[] = $categoryId;
        }
        $stmt = $con->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProductByName($searchTerm) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT products_id, product_name FROM Products WHERE product_name LIKE ? LIMIT 10");
        $stmt->execute(["%$searchTerm%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------ ORDERS ------------------

    public function createOrder($user_id, $order_date, $total_amount, $order_status) {
        $con = $this->opencon();
        $stmt = $con->prepare("INSERT INTO orders (user_id, order_date, total_amount, order_status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $order_date, $total_amount, $order_status]);
        return $con->lastInsertId();
    }

    public function addOrderItem($order_id, $products_id, $quantity, $price) {
        $con = $this->opencon();
        $stmt = $con->prepare("INSERT INTO order_items (order_id, products_id, order_quantity, order_price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$order_id, $products_id, $quantity, $price]);
    }

    public function getAllOrders() {
        $con = $this->opencon();
        $sql = "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.order_status != 'Deleted' ORDER BY o.order_date DESC";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getOrdersPaginated($limit, $offset) {
    $con = $this->opencon();
    $sql = "SELECT o.*, u.username 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_status != 'Deleted' 
            ORDER BY o.order_date DESC 
            LIMIT :limit OFFSET :offset";
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getOrderCount() {
    $con = $this->opencon();
    $sql = "SELECT COUNT(*) as count FROM orders WHERE order_status != 'Deleted'";
    $stmt = $con->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}


    public function getOrderItems($order_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("
            SELECT 
                p.product_name, 
                oi.order_quantity AS quantity, 
                oi.order_price AS price, 
                (oi.order_quantity * oi.order_price) AS subtotal
            FROM order_items oi
            JOIN products p ON oi.products_id = p.products_id
            WHERE oi.order_id = ?
            ");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllOrdersForReport() {
        $con = $this->opencon();
        $stmt = $con->prepare("
        SELECT 
            o.order_id,
            p.product_name,
            oi.order_quantity,
            oi.order_price
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.products_id = p.products_id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderItemsForReport($order_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT p.product_name, oi.order_quantity, oi.order_price 
                           FROM order_items oi 
                           JOIN products p ON oi.products_id = p.products_id 
                           WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getSalesReportData() {
    $con = $this->opencon();
    $stmt = $con->prepare("SELECT * FROM orders ORDER BY order_date DESC");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enrich orders manually just like before
    foreach ($orders as &$order) {
        $stmtPayment = $con->prepare("SELECT SUM(amount_paid) AS total_paid FROM payment_and_invoicing WHERE order_id = ?");
        $stmtPayment->execute([$order['order_id']]);
        $payment = $stmtPayment->fetch(PDO::FETCH_ASSOC);

        $order['total_paid'] = $payment['total_paid'] ?? 0;
        $order['payment_status'] = ($order['total_paid'] >= $order['total_amount']) ? 'Paid' : 'Unpaid';
    }

    return $orders;
}






    public function getOrderById($order_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderDetailsById($order_id) {
    $con = $this->opencon();
    $stmt = $con->prepare("
        SELECT o.*, u.first_name, u.last_name
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    public function updateOrderStatus($order_id, $status = 'Completed') {
        $con = $this->opencon();
        $stmt = $con->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        return $stmt->execute([$status, $order_id]);
    }

    public function softDeleteOrder($order_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("UPDATE orders SET order_status = 'Deleted' WHERE order_id = ?");
        return $stmt->execute([$order_id]);
    }

    public function processOrder($user_id, $products) {
    $con = $this->opencon();
    $order_date = date('Y-m-d H:i:s');
    $total_amount = 0;

    try {
        $con->beginTransaction();

        foreach ($products as $product_id => $quantity) {
            $stmt = $con->prepare("SELECT product_price, product_stock FROM products WHERE products_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product ID $product_id not found.");
            }

            if ($product['product_stock'] < $quantity) {
                throw new Exception("Not enough stock for product ID $product_id.");
            }

            $subtotal = $product['product_price'] * $quantity;
            $total_amount += $subtotal;
        }

        $orderStmt = $con->prepare("INSERT INTO orders (user_id, order_date, total_amount, order_status) VALUES (?, ?, ?, ?)");
        $orderStmt->execute([$user_id, $order_date, $total_amount, 'Pending']);
        $order_id = $con->lastInsertId();

        foreach ($products as $product_id => $quantity) {
            $stmt = $con->prepare("SELECT product_price, product_stock FROM products WHERE products_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $price = $product['product_price'];
            $newStock = $product['product_stock'] - $quantity;

            $itemStmt = $con->prepare("INSERT INTO order_items (order_id, products_id, order_quantity, order_price) VALUES (?, ?, ?, ?)");
            $itemStmt->execute([$order_id, $product_id, $quantity, $price]);

            $updateStock = $con->prepare("UPDATE products SET product_stock = ? WHERE products_id = ?");
            $updateStock->execute([$newStock, $product_id]);
        }

        $con->commit();
        return true;

    } catch (Exception $e) {
        $con->rollBack();
        error_log("Order Processing Error: " . $e->getMessage());
        return $e->getMessage();
    }
}
    // ------------------ SHIPPING ------------------


    public function addShippingDelivery($order_id, $user_id, $tracking_number, $shipping_method, $estimated_delivery_date, $delivery_status) {
        $con = $this->opencon();
        $stmt = $con->prepare("INSERT INTO shipping_and_delivery 
            (order_id, user_id, tracking_number, shipping_method, estimated_delivery_date, delivery_status)
            VALUES (:order_id, :user_id, :tracking_number, :shipping_method, :estimated_delivery_date, :delivery_status)");
        return $stmt->execute([
            ':order_id' => $order_id,
            ':user_id' => $user_id,
            ':tracking_number' => $tracking_number,
            ':shipping_method' => $shipping_method,
            ':estimated_delivery_date' => $estimated_delivery_date,
            ':delivery_status' => $delivery_status
        ]);
    }

    public function getShippingAndDeliveryHistory() {
         $con = $this->opencon();
        $stmt = $con->prepare("
            SELECT sd.*, o.order_date, u.username 
            FROM shipping_and_delivery sd
            JOIN orders o ON sd.order_id = o.order_id
            JOIN users u ON sd.user_id = u.user_id
            ORDER BY sd.estimated_delivery_date DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    // ------------------ PAYMENTS ------------------

    public function addPayment($order_id, $user_id, $payment_method, $amount) {
        $con = $this->opencon();
        $stmt = $con->prepare("INSERT INTO payment_and_invoicing (order_id, user_id, payment_method, amount_paid, payment_date) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$order_id, $user_id, $payment_method, $amount]);
    }

    public function getAllPayments() {
        $con = $this->opencon();
        $stmt = $con->query("SELECT pi.*, u.username FROM payment_and_invoicing pi LEFT JOIN users u ON pi.user_id = u.user_id ORDER BY pi.payment_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPaidByOrderId($order_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT SUM(amount_paid) AS total_paid FROM payment_and_invoicing WHERE order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------ SUPPLIERS ------------------

    public function addSupplier($name, $email, $phone) {
        $con = $this->opencon();
        $stmt = $con->prepare("INSERT INTO supplier (supplier_name, supplier_phonenumber, supplier_email) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $phone, $email]);
    }

    public function addSupplierOrder($supplier_id, $order_date, $expected_delivery_date, $total_cost, $order_status) {
        $con = $this->opencon();
        $stmt = $con->prepare("INSERT INTO supplier_orders (supplier_id, order_date, expected_delivery_date, total_cost, order_status) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$supplier_id, $order_date, $expected_delivery_date, $total_cost, $order_status]);
    }

    public function updateSupplierOrder($supplier_order_id, $order_date, $total_cost) {
    $con = $this->opencon();
    $stmt = $con->prepare("UPDATE supplier_orders SET order_date = ?, total_cost = ? WHERE supplier_order_id = ?");
    return $stmt->execute([$order_date, $total_cost, $supplier_order_id]);
}
    public function updateSupplier($supplier_id, $supplier_name, $supplier_phone, $supplier_email) {
        $con = $this->opencon();
        $stmt = $con->prepare("UPDATE supplier SET supplier_name = ?, supplier_phonenumber = ?, supplier_email = ? WHERE supplier_id = ?");
        return $stmt->execute([$supplier_name, $supplier_phone, $supplier_email, $supplier_id]);
    }



    public function getAllSuppliers() {
        $con = $this->opencon();
        $stmt = $con->query("SELECT * FROM supplier");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSupplierById($supplier_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("DELETE FROM supplier WHERE supplier_id = ?");
        return $stmt->execute([$supplier_id]);
    }

    public function deleteSupplierOrderById($supplier_order_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("DELETE FROM supplier_orders WHERE supplier_order_id = ?");
        return $stmt->execute([$supplier_order_id]);
    }

    public function getSupplierOrders() {
        $con = $this->opencon();
        $stmt = $con->prepare("
            SELECT so.*, s.supplier_name
            FROM supplier_orders so
            JOIN supplier s ON so.supplier_id = s.supplier_id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getSupplierById($supplier_id) {
    $con = $this->opencon();
    $stmt = $con->prepare("SELECT * FROM supplier WHERE supplier_id = ?");
    $stmt->execute([$supplier_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    public function getSupplierOrderById($supplier_order_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM supplier_orders WHERE supplier_order_id = ?");
        $stmt->execute([$supplier_order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function confirmSupplierOrder($supplier_order_id) {
        $con = $this->opencon();
        $stmt = $con->prepare("UPDATE supplier_orders SET order_status = 'Confirmed' WHERE supplier_order_id = ?");
        return $stmt->execute([$supplier_order_id]);
    }

    

    function getCartItems($user_id) {
    $con = $this->opencon();
    $stmt = $con->prepare("
        SELECT c.cart_id, c.products_id, c.cart_quantity, 
               p.product_name, p.product_price, p.category_id, 
               cat.category_name
        FROM cart c 
        JOIN products p ON c.products_id = p.products_id 
        JOIN category cat ON p.category_id = cat.category_id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCartItemById($cart_id) {
    $con = $this->opencon();
    $stmt = $con->prepare("SELECT * FROM cart WHERE cart_id = ?");
    $stmt->execute([$cart_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function removeFromCart($cart_id) {
    $con = $this->opencon();
    $stmt = $con->prepare("DELETE FROM cart WHERE cart_id = ?");
    $stmt->execute([$cart_id]);
}








    // ------------------ INVENTORY TRANSACTIONS ------------------

    public function addInventoryTransaction($type, $products_id, $quantity, $remarks) {
    $types = ['Add' => 1, 'Remove' => -1, 'Sale' => -1, 'Return' => 1, 'Adjustment' => 0];
    $con = $this->opencon();

    $stmt = $con->prepare("INSERT INTO inventory_transactions (transaction_type, products_id, quantity, remarks, transaction_date) VALUES (?, ?, ?, ?, NOW())");
    $result = $stmt->execute([$type, $products_id, $quantity, $remarks]);

    if ($type === 'Adjustment') {
        $update = $con->prepare("UPDATE products SET product_stock = ? WHERE products_id = ?");
        $update->execute([$quantity, $products_id]);
    } elseif (isset($types[$type])) {
        $change = $types[$type] * $quantity;
        $update = $con->prepare("UPDATE products SET product_stock = product_stock + ? WHERE products_id = ?");
        $update->execute([$change, $products_id]);
    }

    return $result; 
}


    public function getAllTransactions() {
        $con = $this->opencon();
        $stmt = $con->query("SELECT it.*, p.product_name FROM inventory_transactions it JOIN products p ON it.products_id = p.products_id ORDER BY it.transaction_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------ DASHBOARD ------------------

    public function getTotalProducts() {
        return $this->opencon()->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    public function getTotalCategories() {
        return $this->opencon()->query("SELECT COUNT(DISTINCT category_id) FROM products")->fetchColumn();
    }

    public function getTotalUsers() {
        return $this->opencon()->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function getTotalSalesThisMonth($month) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT SUM(total_amount) FROM orders WHERE DATE_FORMAT(order_date, '%Y-%m') = ?");
        $stmt->execute([$month]);
        return $stmt->fetchColumn() ?: 0;
    }

    public function getRecentOrders($limit = 5) {
        $con = $this->opencon();
        $stmt = $con->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT $limit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSalesData($limit = 30) {
        $con = $this->opencon();
        $stmt = $con->query("SELECT DATE(order_date) AS date, SUM(total_amount) AS total FROM orders GROUP BY DATE(order_date) ORDER BY date DESC LIMIT $limit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerOrders($userID) {
    $con = $this->opencon();
    $stmt = $con->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5");
    $stmt->execute([$userID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addToCart($user_id, $products_id, $cart_quantity = 1) {
    $conn = $this->opencon();

 
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND products_id = ?");
    $stmt->execute([$user_id, $products_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        
        $stmt = $conn->prepare("UPDATE cart SET cart_quantity = cart_quantity + ? WHERE user_id = ? AND products_id = ?");
        $stmt->execute([$cart_quantity, $user_id, $products_id]);
    } else {
        
        $stmt = $conn->prepare("INSERT INTO cart (user_id, products_id, cart_quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $products_id, $cart_quantity]);
    }
}

public function checkoutOrder($user_id) {
    $con = $this->opencon();

    try {
        $cartStmt = $con->prepare("
            SELECT c.products_id, c.cart_quantity, p.product_price, p.product_stock
            FROM cart c
            JOIN products p ON c.products_id = p.products_id
            WHERE c.user_id = ?
        ");
        $cartStmt->execute([$user_id]);
        $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            return "empty_cart";
        }

        foreach ($cartItems as $item) {
            if ($item['cart_quantity'] > $item['product_stock']) {
                return "stock_error";
            }
        }

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['cart_quantity'] * $item['product_price'];
        }

        $con->beginTransaction();

        $stmt = $con->prepare("INSERT INTO orders (user_id, order_date, total_amount, order_status) VALUES (?, NOW(), ?, 'Pending')");
        $stmt->execute([$user_id, $totalAmount]);
        $order_id = $con->lastInsertId();

        foreach ($cartItems as $item) {
            $itemStmt = $con->prepare("
                INSERT INTO order_items (order_id, products_id, order_quantity, order_price)
                VALUES (?, ?, ?, ?)
            ");
            $itemStmt->execute([
                $order_id,
                $item['products_id'],
                $item['cart_quantity'],
                $item['product_price']
            ]);

            
            $stockStmt = $con->prepare("
                UPDATE products SET product_stock = product_stock - ?
                WHERE products_id = ?
            ");
            $stockStmt->execute([
                $item['cart_quantity'],
                $item['products_id']
            ]);
        }


        $clearStmt = $con->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearStmt->execute([$user_id]);


        $con->commit();
        return "success";

    } catch (PDOException $e) {
        $con->rollBack();
        return "error";
    }
}


public function getOrdersByUser($user_id) {
    $con = $this->opencon();
    $stmt = $con->prepare("SELECT order_id, order_date, total_amount, order_status FROM orders WHERE user_id = ? AND order_status != 'Deleted' ORDER BY order_date DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function updateUserProfile($id, $first_name, $last_name, $username, $email, $contact_number, $address) {
    $con = $this->opencon();
    $stmt = $con->prepare("UPDATE users SET first_name=?, last_name=?, username=?, email=?, contact_number=?, address=? WHERE user_id=?");
    return $stmt->execute([$first_name, $last_name, $username, $email, $contact_number, $address, $id]);
}






}