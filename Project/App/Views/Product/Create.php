<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Create</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; }
        .card { border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .form-label { font-weight: 600; color: #495057; }
        .input-group-text { background-color: transparent; border-right: none; color: #adb5bd; }
        .form-control { border-left: none; }
        .form-control:focus { box-shadow: none; border-color: #dee2e6; }
        .input-group:focus-within .input-group-text { border-color: #764ba2; color: #764ba2; }
        .input-group:focus-within .form-control { border-color: #764ba2; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-4">
                <a href="/project/product/list" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <div class="card p-4 p-md-5">
                <h3 class="fw-bold text-center mb-4">Add New Product</h3>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm small mb-4">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="/project/product/create" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter product name" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Brief details about the product"></textarea>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="price" class="form-label">Price ($)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                            <input type="text" name="price" id="price" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                        Confirm & Create Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm(){
    let name = document.getElementById('name').value;
    let price = document.getElementById('price').value;
    let errors = [];

    if(name.length < 10 || name.length > 100){
        errors.push('Name must be between 10 and 100 characters');
    }
    if(isNaN(price) || price <= 0){
        errors.push('Price must be a number greater than 0');
    }

    if(errors.length > 0){
        alert(errors.join('\n'));
        return false;
    }
    return true;
}
</script>
</body>
</html>
