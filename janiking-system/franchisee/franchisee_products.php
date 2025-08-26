<?php
// ====== PAGE CONFIG ======
$pageTitle = "Buy Products";

// ====== EXAMPLE DATA (replace with DB queries) ======
$categories = ["All Products","Equipment","Safety Gear","Uniforms","Chemicals","Consumables"];

$allProducts = [
  ["id"=>1,"name"=>"Professional Mop System","price"=>24.99,"stock"=>12,"category"=>"Equipment","desc"=>"Professional-grade cleaner for all hard surfaces.","img"=>"https://picsum.photos/seed/mop1/600/400"],
  ["id"=>2,"name"=>"Heavy-Duty Floor Cleaner (2 Gal)","price"=>39.99,"stock"=>8,"category"=>"Chemicals","desc"=>"Concentrated formula for floor stains.","img"=>"https://picsum.photos/seed/chem2/600/400"],
  ["id"=>3,"name"=>"Microfiber Cleaning Cloths (50)","price"=>32.50,"stock"=>25,"category"=>"Consumables","desc"=>"High-quality microfiber cloths that trap dust and dirt.","img"=>"https://picsum.photos/seed/cloths/600/400"],
  ["id"=>4,"name"=>"Nitrile Gloves (Box of 100)","price"=>18.75,"stock"=>40,"category"=>"Safety Gear","desc"=>"Powder-free nitrile gloves for chemical handling.","img"=>"https://picsum.photos/seed/gloves/600/400"],
  ["id"=>5,"name"=>"Backpack Vacuum Pro","price"=>249.00,"stock"=>6,"category"=>"Equipment","desc"=>"Lightweight backpack vacuum with HEPA filter.","img"=>"https://picsum.photos/seed/vac/600/400"],
  ["id"=>6,"name"=>"Professional Spray Bottles (Set of 3)","price"=>12.99,"stock"=>30,"category"=>"Consumables","desc"=>"Color-coded spray bottles with adjustable nozzles.","img"=>"https://picsum.photos/seed/bottles/600/400"],
  ["id"=>7,"name"=>"Polo Uniform Shirt","price"=>19.90,"stock"=>50,"category"=>"Uniforms","desc"=>"Breathable quick-dry polo shirt.","img"=>"https://picsum.photos/seed/shirt/600/400"],
  ["id"=>8,"name"=>"Safety Glasses","price"=>9.50,"stock"=>60,"category"=>"Safety Gear","desc"=>"Impact-resistant protective eyewear.","img"=>"https://picsum.photos/seed/glasses/600/400"],
  ["id"=>9,"name"=>"Window Squeegee 45cm","price"=>17.25,"stock"=>20,"category"=>"Equipment","desc"=>"Streak-free pro squeegee for glass.","img"=>"https://picsum.photos/seed/squeegee/600/400"],
];

// Filters (replace with WHERE in SQL)
$activeCat = $_GET['cat'] ?? "All Products";
$q         = trim($_GET['q'] ?? "");

// Filtered list
$filtered = array_values(array_filter($allProducts, function($p) use ($activeCat,$q){
  $okCat = ($activeCat==="All Products" || $p['category']===$activeCat);
  $okQ   = ($q==="" || stripos($p['name'].$p['desc'],$q)!==false);
  return $okCat && $okQ;
}));

// Pagination (6 per page)
$perPage = 6;
$page    = max(1, (int)($_GET['page'] ?? 1));
$total   = count($filtered);
$totalPages = max(1, (int)ceil($total/$perPage));
$page    = min($page, $totalPages);
$offset  = ($page-1)*$perPage;
$products = array_slice($filtered, $offset, $perPage);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle); ?> | JaniKing</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{ --jk-bg:#e9ecef; --jk-sidebar:#fff; --jk-primary:#004990; }
    body{ background:var(--jk-bg); }
    .jk-layout{ min-height:100vh; display:flex; }
    .jk-sidebar{ width:260px; flex:0 0 260px; background:var(--jk-sidebar); border-right:1px solid rgba(0,0,0,.075); position:sticky; top:0; height:100vh; }
    .jk-main{ flex:1; display:flex; flex-direction:column; }
    .jk-topbar{ position:sticky; top:0; z-index:1020; background:#fff; border-bottom:1px solid rgba(0,0,0,.075); }
    .jk-content{ padding:1.25rem; }
    .jk-logo-placeholder{ width:160px; height:40px; background:#f1f3f5; border:1px dashed #adb5bd; border-radius:.5rem; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:.875rem; }

    .product-card img{ object-fit:cover; height:180px; }
    .cart-item img{ width:64px; height:48px; object-fit:cover; border-radius:.35rem; }
    .price{ font-weight:700; }
    .dropdown-menu-cart{ min-width: 360px; max-height: 440px; overflow-y:auto; }
    @media (max-width: 991.98px){
      .jk-sidebar{ position:fixed; left:-260px; transition:left .25s ease; }
      .jk-sidebar.show{ left:0; box-shadow:0 .5rem 1rem rgba(0,0,0,.15); }
      .jk-backdrop{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.25); z-index:1010; }
      .jk-backdrop.show{ display:block; }
    }
  </style>
</head>
<body>
<div class="jk-layout">
  <!-- Sidebar -->
  <aside id="sidebar" class="jk-sidebar">
    <div class="p-3 border-bottom">
      <a href="#" class="d-flex align-items-center gap-2 text-decoration-none">
        <div class="jk-logo-placeholder">Your Logo</div>
      </a>
    </div>
    <nav class="p-2">
      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="training.php"><i class="bi bi-mortarboard"></i> Training</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="documents.php"><i class="bi bi-folder2-open"></i> Documents</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-graph-up"></i> Reports</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 active" href="buy-products.php"><i class="bi bi-cart3"></i> Buy Products</a></li>
        <li class="nav-item mt-2"><hr></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2" href="#"><i class="bi bi-person-gear"></i> Profile / Settings</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center gap-2 text-danger" href="#"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main -->
  <div class="jk-main">
    <!-- Header -->
    <header class="jk-topbar">
      <div class="container-fluid py-2 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
          <h1 class="h5 m-0"><?php echo htmlspecialchars($pageTitle); ?></h1>
        </div>
        <div class="d-flex align-items-center gap-3">
          <form class="d-none d-md-block" role="search">
            <input class="form-control form-control-sm" type="search" placeholder="Search…" aria-label="Search">
          </form>
          <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i> Michael Reynolds
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#">My Profile</a></li>
              <li><a class="dropdown-item" href="#">Settings</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
            </ul>
          </div>
        </div>
      </div>
  </header>

    <!-- Content -->
    <main class="jk-content">
      <div class="container-fluid">
        <!-- Filters/search + Cart dropdown -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center">
              <?php foreach($categories as $cat): ?>
                <?php
                  $isActive = ($activeCat===$cat);
                  $url = '?'.http_build_query(array_merge($_GET, ['cat'=>$cat,'page'=>1]));
                ?>
                <a href="<?php echo $url; ?>" class="btn btn-sm <?php echo $isActive?'btn-primary':'btn-outline-secondary'; ?>">
                  <?php echo htmlspecialchars($cat); ?>
                </a>
              <?php endforeach; ?>

              <form class="ms-auto me-2" method="get">
                <?php if($activeCat) echo '<input type="hidden" name="cat" value="'.htmlspecialchars($activeCat).'">'; ?>
                <div class="input-group input-group-sm" style="min-width:260px;">
                  <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                  <input name="q" class="form-control" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search products...">
                </div>
              </form>

              <!-- Cart dropdown -->
              <div class="dropdown">
                <button class="btn btn-outline-primary position-relative" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                  <i class="bi bi-cart3"></i>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 dropdown-menu-cart">
                  <h6 class="mb-2">Your Cart</h6>
                  <div id="cart-items" class="vstack gap-3 small">
                    <!-- items injected by JS -->
                  </div>
                  <hr>
                  <div class="d-flex justify-content-between"><span>Subtotal</span><strong id="subtotal">$0.00</strong></div>
                  <div class="d-flex justify-content-between"><span>Tax (8%)</span><strong id="tax">$0.00</strong></div>
                  <div class="d-flex justify-content-between fw-bold"><span>Total</span><strong id="total">$0.00</strong></div>
                  <button id="checkoutBtn" class="btn btn-primary w-100 mt-2" data-bs-toggle="modal" data-bs-target="#checkoutModal" disabled>
                    Proceed to Checkout
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Products grid -->
        <div class="row g-3">
          <?php foreach($products as $p): ?>
          <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 product-card">
              <img src="<?php echo htmlspecialchars($p['img']); ?>" alt="">
              <div class="card-body d-flex flex-column">
                <h6 class="mb-1"><?php echo htmlspecialchars($p['name']); ?></h6>
                <div class="small text-muted mb-2"><?php echo htmlspecialchars($p['desc']); ?></div>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                  <div class="price">$<?php echo number_format($p['price'],2); ?></div>
                  <small class="text-muted"><?php echo $p['stock']>0?'In stock':'Out of stock'; ?></small>
                </div>
              </div>
              <div class="card-footer bg-white">
                <button
                  class="btn btn-primary w-100 add-to-cart"
                  data-id="<?php echo $p['id']; ?>"
                  data-name="<?php echo htmlspecialchars($p['name']); ?>"
                  data-price="<?php echo $p['price']; ?>"
                  data-img="<?php echo htmlspecialchars($p['img']); ?>"
                  <?php echo $p['stock']>0?'':'disabled'; ?>
                >
                  <i class="bi bi-cart-plus me-1"></i>Add to Cart
                </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted small">Showing <?php echo count($products); ?> of <?php echo $total; ?> products</div>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item <?php echo $page<=1?'disabled':''; ?>">
              <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET,['page'=>max(1,$page-1)])); ?>">Previous</a>
            </li>
            <?php for($i=1;$i<=$totalPages;$i++): ?>
              <li class="page-item <?php echo $i==$page?'active':''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET,['page'=>$i])); ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page>=$totalPages?'disabled':''; ?>">
              <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET,['page'=>min($totalPages,$page+1)])); ?>">Next</a>
            </li>
          </ul>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Checkout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="checkoutForm">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <h6>Billing Information</h6>
              <div class="mb-2"><label class="form-label small">Full Name</label><input class="form-control" required value="Michael Reynolds"></div>
              <div class="mb-2"><label class="form-label small">Franchise ID</label><input class="form-control" required value="FR-29384"></div>
              <div class="mb-2"><label class="form-label small">Email Address</label><input type="email" class="form-control" required value="michael.reynolds@example.com"></div>
              <div class="mb-2"><label class="form-label small">Phone Number</label><input class="form-control" placeholder="(555) 123-4567"></div>
            </div>
            <div class="col-md-6">
              <h6>Payment Method</h6>
              <div class="btn-group mb-2" role="group">
                <input type="radio" class="btn-check" name="pay" id="pay1" checked>
                <label class="btn btn-outline-secondary" for="pay1">Credit/Debit Card</label>
                <input type="radio" class="btn-check" name="pay" id="pay2">
                <label class="btn btn-outline-secondary" for="pay2">Transfer to Franchise Account</label>
              </div>
              <div class="mb-2"><label class="form-label small">Card Number</label><input class="form-control" placeholder="XXXX XXXX XXXX XXXX"></div>
              <div class="row g-2">
                <div class="col-6"><label class="form-label small">Expiration Date</label><input class="form-control" placeholder="MM/YY"></div>
                <div class="col-6"><label class="form-label small">CVV</label><input class="form-control" placeholder="123"></div>
              </div>
              <div class="alert alert-light border mt-3">
                <div>Order Total: <strong id="modalTotal">$0.00</strong></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit">Complete Purchase</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Backdrop & scripts -->
<div id="backdrop" class="jk-backdrop"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
const sidebar = document.getElementById('sidebar');
const toggle  = document.getElementById('sidebarToggle');
const backdrop= document.getElementById('backdrop');
function closeSidebar(){ sidebar.classList.remove('show'); backdrop.classList.remove('show'); }
function openSidebar(){ sidebar.classList.add('show'); backdrop.classList.add('show'); }
toggle?.addEventListener('click', () => sidebar.classList.contains('show') ? closeSidebar() : openSidebar());
backdrop?.addEventListener('click', closeSidebar);

// ---- CART LOGIC (client-side; validate again on server at checkout) ----
const TAX_RATE = 0.08;
const $items = document.getElementById('cart-items');
const $count = document.getElementById('cart-count');
const $subtotal = document.getElementById('subtotal');
const $tax = document.getElementById('tax');
const $total = document.getElementById('total');
const $checkoutBtn = document.getElementById('checkoutBtn');
const $modalTotal = document.getElementById('modalTotal');

function loadCart(){ try { return JSON.parse(localStorage.getItem('jk_cart')||'[]'); } catch { return []; } }
function saveCart(cart){ localStorage.setItem('jk_cart', JSON.stringify(cart)); }
function currency(n){ return new Intl.NumberFormat('en-US',{style:'currency',currency:'USD'}).format(n); }

function renderCart(){
  const cart = loadCart();
  $items.innerHTML = '';
  let subtotal = 0;

  cart.forEach((it, idx) => {
    const line = it.price * it.qty;
    subtotal += line;

    const row = document.createElement('div');
    row.className = 'cart-item';
    row.innerHTML = `
      <div class="d-flex align-items-center gap-2">
        <img src="${it.img}" alt="">
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between">
            <strong class="me-2">${it.name}</strong>
            <a href="#" class="text-danger remove-item" data-idx="${idx}" title="Remove"><i class="bi bi-x-lg"></i></a>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-1">
            <div class="btn-group btn-group-sm" role="group" aria-label="Qty">
              <button class="btn btn-outline-secondary qty-dec" data-idx="${idx}">−</button>
              <button class="btn btn-light disabled">${it.qty}</button>
              <button class="btn btn-outline-secondary qty-inc" data-idx="${idx}">+</button>
            </div>
            <span class="fw-semibold">${currency(line)}</span>
          </div>
        </div>
      </div>
      <hr class="mt-2 mb-0">
    `;
    $items.appendChild(row);
  });

  const tax = +(subtotal * TAX_RATE).toFixed(2);
  const total = +(subtotal + tax).toFixed(2);

  $count.textContent = cart.reduce((a,b)=>a+b.qty,0);
  $subtotal.textContent = currency(subtotal);
  $tax.textContent = currency(tax);
  $total.textContent = currency(total);
  $modalTotal.textContent = currency(total);
  $checkoutBtn.disabled = cart.length === 0;
}

function addToCart(prod){
  const cart = loadCart();
  const i = cart.findIndex(x => x.id===prod.id);
  if (i>-1) cart[i].qty += 1; else cart.push({...prod, qty:1});
  saveCart(cart); renderCart();
}

// bind add-to-cart buttons
document.querySelectorAll('.add-to-cart').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    addToCart({
      id: +btn.dataset.id,
      name: btn.dataset.name,
      price: +btn.dataset.price,
      img: btn.dataset.img
    });
  });
});

// handle qty +/- and remove inside dropdown (event delegation)
$items.addEventListener('click', (e)=>{
  const cart = loadCart();
  const inc = e.target.closest('.qty-inc');
  const dec = e.target.closest('.qty-dec');
  const rem = e.target.closest('.remove-item');

  if (inc){
    const idx = +inc.dataset.idx; cart[idx].qty++; saveCart(cart); renderCart();
  }
  if (dec){
    const idx = +dec.dataset.idx; cart[idx].qty = Math.max(1, cart[idx].qty-1); saveCart(cart); renderCart();
  }
  if (rem){
    e.preventDefault();
    const idx = +rem.dataset.idx; cart.splice(idx,1); saveCart(cart); renderCart();
  }
});

// checkout demo
document.getElementById('checkoutForm')?.addEventListener('submit', (e)=>{
  e.preventDefault();
  // TODO: POST cart + billing to server for real processing
  localStorage.removeItem('jk_cart');
  renderCart();
  const modal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
  modal.hide();
  alert('Order placed! (demo)');
});

// initial
renderCart();
</script>
</body>
</html>
