<?php
// Database configuration
$hostname = 'localhost';
$port = '3306';
$username = 'root';
$password = '';
$database = 'nwcheese';
$table = 'firms';

// Function to format URLs properly
function formatUrl($url) {
    if (empty($url)) return '';
    if (!preg_match('/^https?:\/\//i', $url)) {
        return 'https://' . $url;
    }
    return $url;
}

// Initialize variables
$firms = [];
$error = '';

// Database connection and data retrieval
try {
    // Create PDO connection
    $dsn = "mysql:host=$hostname;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Get table columns/structure
    $columnsStmt = $pdo->query("SHOW COLUMNS FROM $table");
    $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    // Query data
    $stmt = $pdo->query("SELECT * FROM $table");
    $firms = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firms Database</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Custom styles -->
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .social-links a {
            margin-right: 5px;
            text-decoration: none;
        }
        .url-cell {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .description-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h1 class="mb-4 text-center">Firms Database</h1>
        
        <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if (empty($firms) && !$error): ?>
        <div class="alert alert-info" role="alert">
            No records found in the database.
        </div>
        <?php endif; ?>
        
        <?php if (!empty($firms)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Firms Data</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                <th><?php echo htmlspecialchars(ucfirst($column)); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($firms as $firm): ?>
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                <td>
                                    <?php 
                                    $value = $firm[$column];
                                    
                                    if ($column == 'url' && !empty($value)) {
                                        $url = formatUrl($value);
                                        echo '<div class="url-cell"><a href="' . htmlspecialchars($url) . '" target="_blank" class="btn btn-sm btn-primary">Visit Website</a></div>';
                                    } 
                                    else if ($column == 'email' && !empty($value)) {
                                        echo '<a href="mailto:' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</a>';
                                    }
                                    else if (in_array($column, ['facebook', 'twitter', 'instagram', 'yelp', 'blog']) && !empty($value)) {
                                        $url = formatUrl($value);
                                        $btnClass = 'btn-primary';
                                        $icon = $column;
                                        
                                        if ($column == 'twitter') $btnClass = 'btn-info';
                                        else if ($column == 'instagram') $btnClass = 'btn-danger';
                                        else if ($column == 'yelp') $btnClass = 'btn-warning';
                                        else if ($column == 'blog') $btnClass = 'btn-success';
                                        
                                        echo '<a href="' . htmlspecialchars($url) . '" target="_blank" class="btn btn-sm ' . $btnClass . '">' . ucfirst($icon) . '</a>';
                                    }
                                    else if ($column == 'description') {
                                        echo '<div class="description-cell" title="' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</div>';
                                    }
                                    else if ($column == 'award') {
                                        echo $value ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
                                    }
                                    else {
                                        echo htmlspecialchars($value ?: '-');
                                    }
                                    ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>
