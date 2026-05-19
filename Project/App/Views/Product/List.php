<?php /** @var ProductModel[] $products */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - List</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .table thead { background-color: #f1f4f9; }
        .btn-add { background: linear-gradient(135deg, #6dd5ed, #2193b0); border: none; color: white; }
        .btn-add:hover { background: linear-gradient(135deg, #2193b0, #6dd5ed); color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/project"><i class="bi bi-box-seam me-2"></i>ZeionStore</a>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Product Catalog</h2>
        <a href="/project/product/create" class="btn btn-add px-4 py-2 rounded-pill shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Create New Product
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="border-0 px-3">ID</th>
                        <th class="border-0">Product Name</th>
                        <th class="border-0">Description</th>
                        <th class="border-0 text-end">Price</th>
                        <th class="border-0 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No products found. Start by creating one!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="px-3 text-muted fw-bold">#<?= $product->getID() ?></td>
                                <td><span class="fw-semibold text-dark"><?= htmlspecialchars($product->getName()) ?></span></td>
                                <td class="text-secondary small"><?= htmlspecialchars($product->getDescription()) ?></td>
                                <td class="text-end fw-bold text-primary">$<?= number_format($product->getPrice(), 2) ?></td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm rounded">
                                        <a href="/project/product/edit/<?= $product->getID() ?>" class="btn btn-white btn-sm text-primary border" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="/project/product/delete/<?= $product->getID() ?>" class="btn btn-white btn-sm text-danger border" 
                                           onclick="return confirm('Are you sure you want to delete this product?')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
