<?php
$success = isset($_GET['success']) && $_GET['success'] == '1';
$error   = isset($_GET['error'])   && $_GET['error']   == '1';

$db_host = 'localhost';
$db_name = 'leads_db';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(50),
        biz_type VARCHAR(100),
        budget VARCHAR(100),
        audience VARCHAR(50),
        template_chosen VARCHAR(100),
        platform VARCHAR(50),
        score INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    $pdo = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Solutions Guide | Grow Your Business</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Mukta+Malar:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter','sans-serif'], tamil: ['Mukta Malar','sans-serif'] },
                    colors: { brand: { 50:'#f0f9ff', 100:'#e0f2fe', 500:'#0ea5e9', 600:'#0284c7', 700:'#0369a1', 900:'#0c4a6e' } }
                }
            }
        }
    </script>
    <style>
        .step-container{display:none;opacity:0;transition:opacity 0.4s ease-in-out}
        .step-container.active{display:block;opacity:1}
        .lang-ta{font-family:'Mukta Malar',sans-serif!important}
        .template-card{border:2px solid transparent;border-radius:12px;overflow:hidden;cursor:pointer;transition:all .2s}
        .template-card:hover{border-color:#0ea5e9;transform:translateY(-2px);box-shadow:0 8px 24px rgba(14,165,233,.2)}
        .template-card.selected{border-color:#0284c7;box-shadow:0 0 0 3px rgba(2,132,199,.25)}
        .template-thumb{width:100%;height:160px;object-fit:cover;display:block}
        .tmpl-preview-frame{width:100%;height:340px;border:none;border-radius:8px}
        .nav-dot{width:8px;height:8px;border-radius:50%;background:#d1d5db;cursor:pointer;transition:background .2s}
        .nav-dot.active{background:#0284c7}
        input.error, select.error{border-color:#ef4444!important;box-shadow:0 0 0 2px rgba(239,68,68,.2)!important}
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col items-center justify-center p-4 sm:p-6">

<?php if ($success): ?>
<div id="success-toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate__animated animate__fadeInDown">
    <i class="fa-solid fa-circle-check text-xl"></i>
    <span>Submitted! We'll be in touch soon.</span>
</div>
<script>setTimeout(()=>document.getElementById('success-toast')?.remove(),4000)</script>
<?php endif; ?>

<?php if ($error): ?>
<div id="error-toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 animate__animated animate__fadeInDown">
    <i class="fa-solid fa-circle-xmark text-xl"></i>
    <span>Something went wrong. Please try again.</span>
</div>
<script>setTimeout(()=>document.getElementById('error-toast')?.remove(),4000)</script>
<?php endif; ?>

    <div class="absolute top-4 right-4 z-50">
        <select id="lang-selector" onchange="switchLanguage(this.value)" class="bg-white border border-gray-300 rounded-lg p-2 text-sm shadow-sm outline-none focus:ring-2 focus:ring-brand-500">
            <option value="en">English</option>
            <option value="ta">தமிழ்</option>
        </select>
    </div>

    <main class="w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden relative mt-10 sm:mt-0">
        <div class="h-1.5 w-full bg-gray-100">
            <div id="progress-bar" class="h-1.5 bg-brand-500 transition-all duration-500 w-1/5"></div>
        </div>

        <div class="p-8 sm:p-12">

            <div id="step-1" class="step-container active text-center">
                <i class="fa-solid fa-handshake text-4xl text-brand-500 mb-4 animate__animated animate__pulse animate__infinite"></i>
                <h1 data-i18n="s1_title" class="text-3xl font-bold mb-2 text-gray-900">Let's grow your business.</h1>
                <p data-i18n="s1_sub" class="text-gray-500 mb-8">To give you the best advice, how comfortable are you with technology?</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button onclick="setAudience('non-tech')" class="p-6 border-2 border-gray-100 rounded-xl hover:border-brand-500 hover:bg-brand-50 transition-all text-left group">
                        <i class="fa-solid fa-mug-hot text-2xl text-gray-400 group-hover:text-brand-500 mb-3 block"></i>
                        <h3 data-i18n="s1_opt1_title" class="font-semibold text-lg text-gray-900">Business Focused</h3>
                        <p data-i18n="s1_opt1_sub" class="text-sm text-gray-500 mt-1">Keep it simple. Show me results and how it grows my bottom line.</p>
                    </button>
                    <button onclick="setAudience('tech')" class="p-6 border-2 border-gray-100 rounded-xl hover:border-brand-500 hover:bg-brand-50 transition-all text-left group">
                        <i class="fa-solid fa-code text-2xl text-gray-400 group-hover:text-brand-500 mb-3 block"></i>
                        <h3 data-i18n="s1_opt2_title" class="font-semibold text-lg text-gray-900">Tech Savvy</h3>
                        <p data-i18n="s1_opt2_sub" class="text-sm text-gray-500 mt-1">I know my stuff. Give me the technical specs and architecture details.</p>
                    </button>
                </div>
            </div>

            <div id="step-2" class="step-container">
                <button onclick="goToStep(1)" class="text-sm text-gray-400 hover:text-gray-600 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i><span data-i18n="back">Back</span>
                </button>
                <h2 data-i18n="s2_title" class="text-2xl font-bold mb-2 text-gray-900">Tell me about your project</h2>
                <p id="step-2-subtitle" class="text-gray-500 mb-6"></p>
                <div class="space-y-5">
                    <div>
                        <label data-i18n="s2_lbl1" class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                        <select id="biz-type" class="w-full border-gray-300 border rounded-lg p-3 outline-none focus:ring-2 focus:ring-brand-500" onchange="updateTemplatePreviewThumb()">
                            <option value="ecommerce"  data-i18n="cat_ecom">E-Commerce / Retail</option>
                            <option value="service"    data-i18n="cat_serv">Service Based (Consulting, Agency)</option>
                            <option value="local"      data-i18n="cat_local">Local Brick &amp; Mortar</option>
                            <option value="startup"    data-i18n="cat_start">Tech Startup / SaaS</option>
                            <option value="realestate" data-i18n="cat_real">Real Estate / Properties</option>
                            <option value="restaurant" data-i18n="cat_rest">Restaurant / Cafe</option>
                            <option value="education"  data-i18n="cat_edu">Education / Tutoring</option>
                            <option value="health"     data-i18n="cat_health">Healthcare / Clinic</option>
                        </select>
                    </div>
                    <div>
                        <label data-i18n="s2_lbl2" class="block text-sm font-medium text-gray-700 mb-1">Estimated Budget</label>
                        <select id="budget" class="w-full border-gray-300 border rounded-lg p-3 outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="starter"    data-i18n="bud_1">Starter ($500 – $1,500)</option>
                            <option value="growth"     data-i18n="bud_2">Growth ($1,500 – $5,000)</option>
                            <option value="enterprise" data-i18n="bud_3">Enterprise ($5,000+)</option>
                        </select>
                    </div>
                    <button onclick="generateSolution()" class="w-full bg-brand-600 hover:bg-brand-500 text-white font-semibold py-3 rounded-lg transition-colors mt-4">
                        <span data-i18n="s2_btn">Find My Solution</span> <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <div id="step-3" class="step-container">
                <button onclick="goToStep(2)" class="text-sm text-gray-400 hover:text-gray-600 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i><span data-i18n="back">Back</span>
                </button>
                <div class="text-center mb-6">
                    <h2 data-i18n="s3_title" class="text-2xl font-bold text-gray-900 mb-2">See The Power of a Website</h2>
                    <p data-i18n="s3_sub" class="text-gray-600 mb-4">Watch how transforming your digital presence brings happiness and growth!</p>
                    <div class="mb-6 rounded-xl overflow-hidden shadow-lg border border-gray-200 bg-black">
                        <iframe width="100%" height="250" src="https://www.youtube.com/embed/n5WiSkvFCMc" title="Business Growth Video" frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen class="w-full"></iframe>
                    </div>
                </div>
                <div class="bg-brand-50 rounded-xl p-6 border border-brand-100 mb-6 text-center">
                    <i id="solution-icon" class="fa-solid fa-rocket text-3xl text-brand-500 mb-2"></i>
                    <h2 id="solution-title" class="text-xl font-bold text-gray-900 mb-2"></h2>
                    <p id="solution-desc" class="text-sm text-gray-600 mb-4"></p>
                    <ul id="solution-benefits" class="space-y-2 text-sm text-gray-700 text-left inline-block"></ul>
                </div>
                <button onclick="loadTemplates()" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-colors">
                    <span data-i18n="s3_btn">View Pre-built Templates</span> <i class="fa-solid fa-layer-group ml-2"></i>
                </button>
            </div>

            <div id="step-4" class="step-container">
                <button onclick="goToStep(3)" class="text-sm text-gray-400 hover:text-gray-600 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i><span data-i18n="back">Back</span>
                </button>
                <div class="text-center mb-4">
                    <h2 data-i18n="s4_title" class="text-2xl font-bold text-gray-900 mb-1">Your Starting Templates</h2>
                    <p data-i18n="s4_sub" class="text-gray-600 text-sm">Click a template below to preview it — then choose your favourite.</p>
                </div>

                <div id="template-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4"></div>

                <div id="preview-wrapper" class="mb-4 hidden">
                    <div class="flex items-center justify-between mb-2">
                        <span id="preview-label" class="text-sm font-semibold text-gray-700"></span>
                        <button onclick="closePreview()" class="text-xs text-gray-400 hover:text-gray-600 flex items-center gap-1"><i class="fa-solid fa-xmark"></i> Close</button>
                    </div>
                    <iframe id="template-frame" class="tmpl-preview-frame border border-gray-200 rounded-xl shadow-md" srcdoc=""></iframe>
                </div>

                <div id="selected-template-bar" class="hidden mb-4 flex items-center gap-3 p-3 bg-brand-50 rounded-xl border border-brand-100">
                    <i class="fa-solid fa-circle-check text-brand-600 text-lg"></i>
                    <span class="text-sm font-medium text-brand-700">Selected: <strong id="selected-label"></strong></span>
                </div>

                <button onclick="goToStep(5)" class="w-full bg-brand-600 hover:bg-brand-500 text-white font-semibold py-3 rounded-lg transition-colors">
                    <span data-i18n="s4_btn">I Love This, Let's Connect!</span> <i class="fa-solid fa-heart ml-2"></i>
                </button>
            </div>

            <div id="step-5" class="step-container text-center">
                <button onclick="goToStep(4)" class="text-sm text-gray-400 hover:text-gray-600 mb-6 flex items-center gap-2 justify-start w-full">
                    <i class="fa-solid fa-arrow-left"></i><span data-i18n="back">Back</span>
                </button>
                <h2 data-i18n="s5_title" class="text-2xl font-bold text-gray-900 mb-2">Let's make it happen.</h2>
                <p data-i18n="s5_sub" class="text-gray-500 mb-6">Enter your details and choose how you'd like to connect.</p>
                <div class="space-y-4 text-left">
                    <input type="text"  id="contact-name"  data-placeholder="s5_name"  placeholder="Your Name"  class="w-full border-gray-300 border rounded-lg p-3 outline-none focus:ring-2 focus:ring-brand-500">
                    <input type="email" id="contact-email" data-placeholder="s5_email" placeholder="Your Email" class="w-full border-gray-300 border rounded-lg p-3 outline-none focus:ring-2 focus:ring-brand-500">
                    <input type="tel"   id="contact-phone" data-placeholder="s5_phone" placeholder="Phone / WhatsApp Number" class="w-full border-gray-300 border rounded-lg p-3 outline-none focus:ring-2 focus:ring-brand-500">
                    <!-- <div 
                    class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6"
                    > --><div>
                    
                        <!-- <button onclick="submitLead('whatsapp')" class="flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1da851] text-white font-semibold py-3 rounded-lg transition-colors shadow-lg">
                            <i class="fa-brands fa-whatsapp text-xl"></i><span data-i18n="btn_wa">WhatsApp</span>
                        </button> -->
                        <button onclick="submitLead('email')" class="flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white w-full font-semibold py-3 rounded-lg transition-colors shadow-lg">
                            <i class="fa-solid fa-envelope text-xl"></i><span data-i18n="btn_em">Send Email</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <form id="lead-form" action="email.php" method="POST" style="display:none">
        <input type="hidden" name="name"             id="f-name">
        <input type="hidden" name="email"            id="f-email">
        <input type="hidden" name="phone"            id="f-phone">
        <input type="hidden" name="biz_type"         id="f-biz">
        <input type="hidden" name="budget"           id="f-budget">
        <input type="hidden" name="audience"         id="f-audience">
        <input type="hidden" name="template_chosen"  id="f-template">
        <input type="hidden" name="platform"         id="f-platform">
    </form>

<script>
const state = { audience: null, bizType: null, budget: null, currentLang: 'en', selectedTemplate: null };

const i18n = {
    en: {
        back:"Back", s1_title:"Let's grow your business.", s1_sub:"To give you the best advice, how comfortable are you with technology?",
        s1_opt1_title:"Business Focused", s1_opt1_sub:"Keep it simple. Show me results and how it grows my bottom line.",
        s1_opt2_title:"Tech Savvy", s1_opt2_sub:"I know my stuff. Give me the technical specs and architecture details.",
        s2_title:"Tell me about your project", s2_sub_nontech:"Don't worry about the tech terms. Just tell me your goals.",
        s2_sub_tech:"Let's align your stack and scalability requirements.",
        s2_lbl1:"Business Type", s2_lbl2:"Estimated Budget",
        cat_ecom:"E-Commerce / Retail", cat_serv:"Service Based (Consulting, Agency)", cat_local:"Local Brick & Mortar",
        cat_start:"Tech Startup / SaaS", cat_real:"Real Estate / Properties", cat_rest:"Restaurant / Cafe",
        cat_edu:"Education / Tutoring", cat_health:"Healthcare / Clinic",
        bud_1:"Starter ($500 – $1,500)", bud_2:"Growth ($1,500 – $5,000)", bud_3:"Enterprise ($5,000+)",
        s2_btn:"Find My Solution", s3_title:"See The Power of a Website",
        s3_sub:"Watch how transforming your digital presence brings happiness and growth!",
        s3_btn:"View Pre-built Templates", s4_title:"Your Starting Templates",
        s4_sub:"Click a template below to preview it — then choose your favourite.",
        s4_btn:"I Love This, Let's Connect!", s5_title:"Let's make it happen.",
        s5_sub:"Enter your details and choose how you'd like to connect.",
        s5_name:"Your Name", s5_email:"Your Email", s5_phone:"Phone / WhatsApp Number",
        btn_wa:"WhatsApp", btn_em:"Send Email",
        sol_ecom:"E-Commerce Conversion Engine", sol_local:"Local SEO & Footfall Booster", sol_gen:"Lead Generation Machine"
    },
    ta: {
        back:"பின்செல்", s1_title:"உங்கள் வணிகத்தை வளர்ப்போம்.", s1_sub:"சிறந்த ஆலோசனை தர, தொழில்நுட்பத்தில் நீங்கள் எவ்வளவு வசதியாக உள்ளீர்கள்?",
        s1_opt1_title:"வணிக நோக்கம்", s1_opt1_sub:"எளிமையாக இருக்கட்டும். லாபத்தை எப்படி அதிகரிப்பது என காட்டுங்கள்.",
        s1_opt2_title:"தொழில்நுட்ப அறிவு", s1_opt2_sub:"தொழில்நுட்ப விவரங்களை கொடுங்கள்.",
        s2_title:"உங்கள் திட்டத்தைப் பற்றி சொல்லுங்கள்",
        s2_sub_nontech:"தொழில்நுட்ப சொற்களைப் பற்றி கவலைப்பட வேண்டாம். உங்கள் இலக்குகளை கூறுங்கள்.",
        s2_sub_tech:"உங்கள் தொழில்நுட்ப தேவைகளை அமைப்போம்.",
        s2_lbl1:"வணிக வகை", s2_lbl2:"மதிப்பிடப்பட்ட பட்ஜெட்",
        cat_ecom:"மின்-வணிகம்", cat_serv:"சேவை அடிப்படை", cat_local:"உள்ளூர் வணிகம்",
        cat_start:"ஸ்டார்ட்அப்", cat_real:"ரியல் எஸ்டேட்", cat_rest:"உணவகம்",
        cat_edu:"கல்வி", cat_health:"சுகாதாரம்",
        bud_1:"ஆரம்பம்", bud_2:"வளர்ச்சி", bud_3:"நிறுவனம்",
        s2_btn:"எனது தீர்வை கண்டுபிடி", s3_title:"இணையதளத்தின் சக்தியைப் பாருங்கள்",
        s3_sub:"டிஜிட்டல் மாற்றம் எவ்வாறு வளர்ச்சி தருகிறது என்பதைப் பாருங்கள்!",
        s3_btn:"மாதிரி வடிவங்களை காண்க", s4_title:"உங்கள் மாதிரி வடிவங்கள்",
        s4_sub:"கீழே ஒரு மாதிரியை கிளிக் செய்து முன்னோட்டம் காணுங்கள்.",
        s4_btn:"இது அருமை, தொடர்புகொள்வோம்!", s5_title:"இதை சாத்தியமாக்குவோம்.",
        s5_sub:"உங்கள் விவரங்களை உள்ளிட்டு இணைவதற்கான வழியை தேர்வு செய்க.",
        s5_name:"உங்கள் பெயர்", s5_email:"உங்கள் மின்னஞ்சல்", s5_phone:"தொலைபேசி எண்",
        btn_wa:"வாட்ஸ்அப்", btn_em:"மின்னஞ்சல் அனுப்பு",
        sol_ecom:"மின்-வணிக விற்பனை இயந்திரம்", sol_local:"உள்ளூர் SEO இயந்திரம்", sol_gen:"முன்னணி உருவாக்கும் இயந்திரம்"
    }
};

const templates = {
    ecommerce: [
        {
            name: "ShopLux – Fashion Store",
            thumb: "https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#0f0f0f;color:#fff;min-height:100vh">
  <nav style="display:flex;justify-content:space-between;align-items:center;padding:16px 24px;border-bottom:1px solid #222">
    <span style="font-size:20px;font-weight:700;letter-spacing:4px">SHOPLUX</span>
    <div style="display:flex;gap:20px;font-size:13px;color:#aaa">
      <a style="color:#aaa;text-decoration:none">Women</a><a style="color:#aaa;text-decoration:none">Men</a><a style="color:#aaa;text-decoration:none">Sale</a>
      <span style="cursor:pointer">🛒 3</span>
    </div>
  </nav>
  <div style="display:grid;grid-template-columns:1fr 1fr;height:320px">
    <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&q=80" style="width:100%;height:100%;object-fit:cover">
    <div style="display:flex;flex-direction:column;justify-content:center;padding:40px">
      <p style="color:#e2a800;font-size:12px;letter-spacing:3px;margin-bottom:12px">NEW ARRIVALS 2025</p>
      <h1 style="font-size:32px;font-weight:800;margin-bottom:12px;line-height:1.2">Elevate Your<br>Style</h1>
      <p style="color:#888;font-size:13px;margin-bottom:20px">Curated collections for the modern wardrobe</p>
      <button style="background:#e2a800;color:#000;border:none;padding:12px 28px;font-weight:700;font-size:13px;cursor:pointer;border-radius:2px;width:fit-content">SHOP NOW</button>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;padding:24px">
    <div style="background:#1a1a1a;border-radius:8px;overflow:hidden">
      <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&q=80" style="width:100%;height:140px;object-fit:cover">
      <div style="padding:12px"><p style="font-size:13px;font-weight:600">Watch Classic</p><p style="color:#e2a800;font-weight:700;margin-top:4px">$249</p></div>
    </div>
    <div style="background:#1a1a1a;border-radius:8px;overflow:hidden">
      <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=300&q=80" style="width:100%;height:140px;object-fit:cover">
      <div style="padding:12px"><p style="font-size:13px;font-weight:600">Leather Bag</p><p style="color:#e2a800;font-weight:700;margin-top:4px">$189</p></div>
    </div>
    <div style="background:#1a1a1a;border-radius:8px;overflow:hidden">
      <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&q=80" style="width:100%;height:140px;object-fit:cover">
      <div style="padding:12px"><p style="font-size:13px;font-weight:600">Sport Shoes</p><p style="color:#e2a800;font-weight:700;margin-top:4px">$129</p></div>
    </div>
  </div>
</div>`
        },
        {
            name: "FreshCart – Grocery",
            thumb: "https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#f0fdf4;min-height:100vh">
  <nav style="background:#16a34a;color:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:18px;font-weight:700">🥦 FreshCart</span>
    <div style="display:flex;gap:16px;font-size:13px">
      <span>Fruits</span><span>Veggies</span><span>Dairy</span><span>🛒</span>
    </div>
  </nav>
  <div style="background:linear-gradient(135deg,#16a34a,#4ade80);color:#fff;padding:32px 24px;text-align:center">
    <p style="font-size:12px;letter-spacing:2px;opacity:.8;margin-bottom:8px">FREE DELIVERY OVER $50</p>
    <h1 style="font-size:28px;font-weight:800;margin-bottom:8px">Fresh & Organic<br>Delivered Daily</h1>
    <button style="background:#fff;color:#16a34a;border:none;padding:10px 24px;font-weight:700;border-radius:24px;cursor:pointer;margin-top:12px">Order Now</button>
  </div>
  <div style="padding:20px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
    <div style="background:#fff;border-radius:12px;padding:12px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.06)">
      <img src="https://images.unsplash.com/photo-1601004890684-d8cbf643f5f2?w=200&q=80" style="width:80px;height:80px;object-fit:cover;border-radius:50%;margin:0 auto 8px">
      <p style="font-size:12px;font-weight:600">Mangoes</p>
      <p style="color:#16a34a;font-size:13px;font-weight:700">$4.99</p>
      <button style="background:#16a34a;color:#fff;border:none;border-radius:16px;padding:4px 12px;font-size:11px;cursor:pointer;margin-top:6px">Add</button>
    </div>
    <div style="background:#fff;border-radius:12px;padding:12px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.06)">
      <img src="https://images.unsplash.com/photo-1582284540020-8acbe03f4924?w=200&q=80" style="width:80px;height:80px;object-fit:cover;border-radius:50%;margin:0 auto 8px">
      <p style="font-size:12px;font-weight:600">Strawberries</p>
      <p style="color:#16a34a;font-size:13px;font-weight:700">$6.99</p>
      <button style="background:#16a34a;color:#fff;border:none;border-radius:16px;padding:4px 12px;font-size:11px;cursor:pointer;margin-top:6px">Add</button>
    </div>
    <div style="background:#fff;border-radius:12px;padding:12px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.06)">
      <img src="https://images.unsplash.com/photo-1587735243615-c03f25aaff15?w=200&q=80" style="width:80px;height:80px;object-fit:cover;border-radius:50%;margin:0 auto 8px">
      <p style="font-size:12px;font-weight:600">Blueberries</p>
      <p style="color:#16a34a;font-size:13px;font-weight:700">$5.49</p>
      <button style="background:#16a34a;color:#fff;border:none;border-radius:16px;padding:4px 12px;font-size:11px;cursor:pointer;margin-top:6px">Add</button>
    </div>
  </div>
</div>`
        },
        {
            name: "TechHub – Electronics",
            thumb: "https://images.unsplash.com/photo-1518770660439-4636190af475?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#0d1117;color:#f0f6ff;min-height:100vh">
  <nav style="padding:14px 24px;display:flex;justify-content:space-between;background:#161b22;border-bottom:1px solid #30363d">
    <span style="font-size:18px;font-weight:700;color:#58a6ff">TechHub</span>
    <div style="display:flex;gap:16px;font-size:12px;color:#8b949e;align-items:center">
      <span>Laptops</span><span>Phones</span><span>Audio</span><span style="color:#58a6ff">🛒 2</span>
    </div>
  </nav>
  <div style="padding:32px 24px;text-align:center;background:linear-gradient(180deg,#161b22,#0d1117)">
    <span style="background:#238636;color:#fff;font-size:11px;padding:3px 10px;border-radius:12px;font-weight:600">FLASH SALE — 30% OFF</span>
    <h1 style="font-size:28px;font-weight:800;margin:16px 0 8px">Next-Gen Tech<br>at Unbeatable Prices</h1>
    <p style="color:#8b949e;font-size:13px;margin-bottom:20px">Latest laptops, phones & gadgets delivered fast</p>
    <button style="background:#58a6ff;color:#0d1117;border:none;padding:12px 28px;font-weight:700;border-radius:6px;cursor:pointer">Shop Deals</button>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:16px 24px">
    <div style="background:#161b22;border:1px solid #30363d;border-radius:10px;padding:16px">
      <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300&q=80" style="width:100%;height:100px;object-fit:cover;border-radius:6px;margin-bottom:10px">
      <p style="font-size:13px;font-weight:600">MacBook Pro M3</p>
      <p style="color:#58a6ff;font-weight:700;margin-top:4px">$1,299</p>
      <button style="background:#238636;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:11px;cursor:pointer;margin-top:8px;width:100%">Add to Cart</button>
    </div>
    <div style="background:#161b22;border:1px solid #30363d;border-radius:10px;padding:16px">
      <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&q=80" style="width:100%;height:100px;object-fit:cover;border-radius:6px;margin-bottom:10px">
      <p style="font-size:13px;font-weight:600">iPhone 16 Pro</p>
      <p style="color:#58a6ff;font-weight:700;margin-top:4px">$999</p>
      <button style="background:#238636;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:11px;cursor:pointer;margin-top:8px;width:100%">Add to Cart</button>
    </div>
  </div>
</div>`
        }
    ],
    restaurant: [
        {
            name: "La Bella – Italian",
            thumb: "https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:Georgia,serif}</style>
<div style="background:#1a0a00;color:#f5e6c8;min-height:100vh">
  <nav style="display:flex;justify-content:space-between;align-items:center;padding:20px 28px;border-bottom:1px solid #3a2010">
    <span style="font-size:22px;font-style:italic;color:#e2a800">La Bella</span>
    <div style="display:flex;gap:20px;font-size:12px;color:#c9a06a;letter-spacing:2px">
      <span>MENU</span><span>RESERVE</span><span>ABOUT</span>
    </div>
  </nav>
  <div style="position:relative;overflow:hidden">
    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80" style="width:100%;height:240px;object-fit:cover;opacity:.6">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;width:100%">
      <p style="font-size:12px;letter-spacing:4px;color:#e2a800;margin-bottom:8px">EST. 1987</p>
      <h1 style="font-size:36px;font-style:italic;text-shadow:2px 2px 8px rgba(0,0,0,.8)">Authentic Italian Cuisine</h1>
      <button style="background:#e2a800;color:#1a0a00;border:none;padding:10px 24px;font-weight:700;font-size:12px;letter-spacing:2px;cursor:pointer;margin-top:16px">RESERVE TABLE</button>
    </div>
  </div>
  <div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div style="border:1px solid #3a2010;border-radius:8px;overflow:hidden">
      <img src="https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=300&q=80" style="width:100%;height:120px;object-fit:cover">
      <div style="padding:12px"><p style="font-weight:600;color:#e2a800">Margherita Pizza</p><p style="font-size:12px;color:#a08060;margin-top:4px">Classic tomato, mozzarella & basil</p><p style="font-weight:700;margin-top:8px">$18</p></div>
    </div>
    <div style="border:1px solid #3a2010;border-radius:8px;overflow:hidden">
      <img src="https://images.unsplash.com/photo-1551183053-bf91798d702f?w=300&q=80" style="width:100%;height:120px;object-fit:cover">
      <div style="padding:12px"><p style="font-weight:600;color:#e2a800">Pasta Carbonara</p><p style="font-size:12px;color:#a08060;margin-top:4px">Pancetta, egg, pecorino & pepper</p><p style="font-weight:700;margin-top:8px">$22</p></div>
    </div>
  </div>
</div>`
        },
        {
            name: "SushiZen – Japanese",
            thumb: "https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#fafafa;min-height:100vh">
  <nav style="background:#c0392b;color:#fff;padding:16px 24px;display:flex;justify-content:space-between">
    <span style="font-size:20px;font-weight:700;letter-spacing:2px">寿司ZEN</span>
    <div style="display:flex;gap:16px;font-size:12px;align-items:center"><span>Menu</span><span>Reserve</span><span>Order</span></div>
  </nav>
  <div style="background:linear-gradient(135deg,#c0392b,#922b21);color:#fff;padding:36px 24px;text-align:center">
    <p style="font-size:11px;letter-spacing:4px;opacity:.8;margin-bottom:8px">AUTHENTIC JAPANESE</p>
    <h1 style="font-size:30px;font-weight:800;margin-bottom:8px">Art of Sushi</h1>
    <p style="opacity:.8;font-size:13px;margin-bottom:20px">Hand-crafted rolls, premium omakase & sake pairings</p>
    <div style="display:flex;gap:12px;justify-content:center">
      <button style="background:#fff;color:#c0392b;border:none;padding:10px 20px;font-weight:700;border-radius:4px;cursor:pointer">View Menu</button>
      <button style="background:transparent;color:#fff;border:2px solid rgba(255,255,255,.5);padding:10px 20px;font-weight:700;border-radius:4px;cursor:pointer">Reserve</button>
    </div>
  </div>
  <div style="padding:20px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);text-align:center;padding-bottom:12px">
      <img src="https://images.unsplash.com/photo-1617196034183-421b4917c92d" style="width:100%;height:100px;object-fit:cover">
      <p style="font-size:12px;font-weight:700;margin-top:8px">Dragon Roll</p>
      <p style="color:#c0392b;font-weight:700;font-size:13px">$16</p>
    </div>
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);text-align:center;padding-bottom:12px">
      <img src="https://images.unsplash.com/photo-1553621042-f6e147245754?w=200&q=80" style="width:100%;height:100px;object-fit:cover">
      <p style="font-size:12px;font-weight:700;margin-top:8px">Salmon Nigiri</p>
      <p style="color:#c0392b;font-weight:700;font-size:13px">$12</p>
    </div>
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);text-align:center;padding-bottom:12px">
      <img src="https://images.unsplash.com/photo-1562802378-063ec186a863?w=200&q=80" style="width:100%;height:100px;object-fit:cover">
      <p style="font-size:12px;font-weight:700;margin-top:8px">Ramen Bowl</p>
      <p style="color:#c0392b;font-weight:700;font-size:13px">$14</p>
    </div>
  </div>
</div>`
        }
    ],
    realestate: [
        {
            name: "PrimeEstates – Luxury",
            thumb: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#f8f5f0;min-height:100vh">
  <nav style="background:#1a1a1a;color:#fff;padding:16px 28px;display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:18px;font-weight:300;letter-spacing:4px">PRIME<strong>ESTATES</strong></span>
    <div style="display:flex;gap:20px;font-size:12px;color:#ccc;letter-spacing:1px">
      <span>BUY</span><span>SELL</span><span>RENT</span><span>AGENTS</span>
    </div>
  </nav>
  <div style="background:linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.5)),url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80') center/cover;color:#fff;padding:48px 28px;text-align:center">
    <h1 style="font-size:32px;font-weight:300;letter-spacing:2px;margin-bottom:16px">Find Your Dream Home</h1>
    <div style="display:flex;max-width:500px;margin:0 auto;background:#fff;border-radius:4px;overflow:hidden">
      <input style="flex:1;padding:12px 16px;border:none;outline:none;color:#333;font-size:13px" placeholder="City, ZIP, neighborhood...">
      <button style="background:#b8860b;color:#fff;border:none;padding:12px 20px;font-size:13px;font-weight:600;cursor:pointer">Search</button>
    </div>
  </div>
  <div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)">
      <img src="https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=400&q=80" style="width:100%;height:140px;object-fit:cover">
      <div style="padding:14px">
        <span style="background:#d4af37;color:#fff;font-size:10px;padding:2px 8px;border-radius:10px;font-weight:600">FOR SALE</span>
        <h3 style="font-size:14px;font-weight:700;margin:8px 0 4px">Modern Villa, Beverly Hills</h3>
        <p style="color:#b8860b;font-weight:700;font-size:16px">$2,450,000</p>
        <p style="color:#888;font-size:12px;margin-top:4px">4 bed · 3 bath · 3,200 sqft</p>
      </div>
    </div>
    <div style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)">
      <img src="https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?w=400&q=80" style="width:100%;height:140px;object-fit:cover">
      <div style="padding:14px">
        <span style="background:#0ea5e9;color:#fff;font-size:10px;padding:2px 8px;border-radius:10px;font-weight:600">FOR RENT</span>
        <h3 style="font-size:14px;font-weight:700;margin:8px 0 4px">Penthouse, Manhattan</h3>
        <p style="color:#b8860b;font-weight:700;font-size:16px">$8,500/mo</p>
        <p style="color:#888;font-size:12px;margin-top:4px">3 bed · 2 bath · 2,800 sqft</p>
      </div>
    </div>
  </div>
</div>`
        }
    ],
    health: [
        {
            name: "MediCare Plus – Clinic",
            thumb: "https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#f0f9ff;min-height:100vh">
  <nav style="background:#0e7490;color:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:18px;font-weight:700">🏥 MediCare+</span>
    <div style="display:flex;gap:16px;font-size:12px"><span>Services</span><span>Doctors</span><span>Book</span></div>
  </nav>
  <div style="background:linear-gradient(135deg,#0e7490,#0284c7);color:#fff;padding:32px 24px">
    <h1 style="font-size:26px;font-weight:700;margin-bottom:8px">Quality Healthcare,<br>Close to Home</h1>
    <p style="opacity:.85;font-size:13px;margin-bottom:20px">Expert doctors · Fast appointments · Modern facilities</p>
    <button style="background:#fff;color:#0e7490;border:none;padding:10px 24px;font-weight:700;border-radius:24px;cursor:pointer">Book Appointment</button>
  </div>
  <div style="padding:20px">
    <p style="font-size:13px;font-weight:700;color:#0e7490;margin-bottom:12px;letter-spacing:1px">OUR SERVICES</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
      <div style="background:#fff;border-radius:10px;padding:16px;display:flex;align-items:center;gap:10px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
        <span style="font-size:24px">🩺</span><div><p style="font-weight:600;font-size:13px">General Care</p><p style="color:#888;font-size:11px">Walk-ins welcome</p></div>
      </div>
      <div style="background:#fff;border-radius:10px;padding:16px;display:flex;align-items:center;gap:10px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
        <span style="font-size:24px">🦷</span><div><p style="font-weight:600;font-size:13px">Dental</p><p style="color:#888;font-size:11px">Full dental care</p></div>
      </div>
      <div style="background:#fff;border-radius:10px;padding:16px;display:flex;align-items:center;gap:10px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
        <span style="font-size:24px">👁️</span><div><p style="font-weight:600;font-size:13px">Eye Clinic</p><p style="color:#888;font-size:11px">Vision & glasses</p></div>
      </div>
      <div style="background:#fff;border-radius:10px;padding:16px;display:flex;align-items:center;gap:10px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
        <span style="font-size:24px">🧬</span><div><p style="font-weight:600;font-size:13px">Lab Tests</p><p style="color:#888;font-size:11px">Results in 24h</p></div>
      </div>
    </div>
  </div>
</div>`
        }
    ],
    education: [
        {
            name: "EduPath – Online Learning",
            thumb: "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#fefce8;min-height:100vh">
  <nav style="background:#854d0e;color:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:18px;font-weight:700">📚 EduPath</span>
    <div style="display:flex;gap:16px;font-size:12px"><span>Courses</span><span>Tutors</span><span>Enroll</span></div>
  </nav>
  <div style="background:linear-gradient(135deg,#854d0e,#ca8a04);color:#fff;padding:32px 24px;text-align:center">
    <h1 style="font-size:26px;font-weight:800;margin-bottom:8px">Learn. Grow. Succeed.</h1>
    <p style="opacity:.85;font-size:13px;margin-bottom:20px">1,000+ courses · Expert tutors · Certificates</p>
    <button style="background:#fff;color:#854d0e;border:none;padding:10px 24px;font-weight:700;border-radius:24px;cursor:pointer">Explore Courses</button>
  </div>
  <div style="padding:20px">
    <p style="font-size:13px;font-weight:700;color:#854d0e;margin-bottom:12px">POPULAR COURSES</p>
    <div style="display:grid;grid-template-columns:1fr;gap:10px">
      <div style="background:#fff;border-radius:10px;padding:14px;display:flex;align-items:center;gap:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
        <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=100&q=80" style="width:60px;height:60px;object-fit:cover;border-radius:8px">
        <div style="flex:1"><p style="font-weight:600;font-size:13px">Full-Stack Web Dev</p><p style="color:#888;font-size:11px">⭐ 4.9 · 12,000 students</p></div>
        <span style="color:#ca8a04;font-weight:700">$49</span>
      </div>
      <div style="background:#fff;border-radius:10px;padding:14px;display:flex;align-items:center;gap:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
        <img src="https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?w=100&q=80" style="width:60px;height:60px;object-fit:cover;border-radius:8px">
        <div style="flex:1"><p style="font-weight:600;font-size:13px">Data Science Bootcamp</p><p style="color:#888;font-size:11px">⭐ 4.8 · 8,500 students</p></div>
        <span style="color:#ca8a04;font-weight:700">$79</span>
      </div>
    </div>
  </div>
</div>`
        }
    ],
    local: [
        {
            name: "LocalPro – Services",
            thumb: "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#fff;min-height:100vh">
  <nav style="background:#1e3a5f;color:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:18px;font-weight:700">🔧 LocalPro</span>
    <div style="display:flex;gap:16px;font-size:12px"><span>Services</span><span>Reviews</span><span>Book</span></div>
  </nav>
  <div style="background:linear-gradient(135deg,#1e3a5f,#2563eb);color:#fff;padding:32px 24px;text-align:center">
    <h1 style="font-size:26px;font-weight:700;margin-bottom:8px">Trusted Local Services</h1>
    <p style="opacity:.85;font-size:13px;margin-bottom:20px">Plumbing · Electrical · Cleaning · Repairs</p>
    <button style="background:#fff;color:#1e3a5f;border:none;padding:10px 24px;font-weight:700;border-radius:24px;cursor:pointer">Get a Free Quote</button>
  </div>
  <div style="padding:20px">
    <div style="background:#f0f9ff;border-radius:10px;padding:16px;margin-bottom:12px;display:flex;align-items:center;gap:12px">
      <span style="font-size:28px">⭐</span>
      <div><p style="font-weight:700">4.9/5 Rating</p><p style="color:#888;font-size:12px">Based on 2,400+ reviews</p></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
      <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;text-align:center">
        <span style="font-size:28px;display:block;margin-bottom:6px">🪠</span>
        <p style="font-size:13px;font-weight:600">Plumbing</p>
        <p style="color:#2563eb;font-size:12px;font-weight:600;margin-top:4px">From $79</p>
      </div>
      <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;text-align:center">
        <span style="font-size:28px;display:block;margin-bottom:6px">💡</span>
        <p style="font-size:13px;font-weight:600">Electrical</p>
        <p style="color:#2563eb;font-size:12px;font-weight:600;margin-top:4px">From $99</p>
      </div>
    </div>
  </div>
</div>`
        }
    ],
    service: [
        {
            name: "AgencyX – Digital Marketing",
            thumb: "https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#09090b;color:#fafafa;min-height:100vh">
  <nav style="padding:16px 28px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #27272a">
    <span style="font-size:18px;font-weight:800;background:linear-gradient(135deg,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent">Agency X</span>
    <div style="display:flex;gap:20px;font-size:12px;color:#a1a1aa"><span>Work</span><span>Services</span><span>Team</span><span style="color:#a855f7">Contact</span></div>
  </nav>
  <div style="padding:48px 28px;text-align:center">
    <span style="background:#27272a;color:#a855f7;font-size:11px;padding:4px 12px;border-radius:12px;letter-spacing:1px">🏆 AWARD-WINNING AGENCY</span>
    <h1 style="font-size:36px;font-weight:900;margin:20px 0 12px;line-height:1.15">We Build Brands<br><span style="background:linear-gradient(135deg,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent">That Convert</span></h1>
    <p style="color:#a1a1aa;font-size:14px;max-width:400px;margin:0 auto 24px">SEO · Paid Ads · Web Design · Social Media · Email Funnels</p>
    <div style="display:flex;gap:12px;justify-content:center">
      <button style="background:linear-gradient(135deg,#a855f7,#ec4899);color:#fff;border:none;padding:12px 28px;font-weight:700;border-radius:6px;cursor:pointer">Start Project</button>
      <button style="background:transparent;color:#fafafa;border:1px solid #3f3f46;padding:12px 28px;font-weight:600;border-radius:6px;cursor:pointer">Our Work</button>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:#27272a;margin:0 28px;border-radius:10px;overflow:hidden">
    <div style="background:#09090b;padding:20px;text-align:center"><p style="font-size:24px;font-weight:900;color:#a855f7">250+</p><p style="font-size:11px;color:#71717a;margin-top:4px">Clients Worldwide</p></div>
    <div style="background:#09090b;padding:20px;text-align:center"><p style="font-size:24px;font-weight:900;color:#ec4899">$8M+</p><p style="font-size:11px;color:#71717a;margin-top:4px">Revenue Generated</p></div>
    <div style="background:#09090b;padding:20px;text-align:center"><p style="font-size:24px;font-weight:900;color:#a855f7">98%</p><p style="font-size:11px;color:#71717a;margin-top:4px">Client Retention</p></div>
  </div>
</div>`
        }
    ],
    startup: [
        {
            name: "LaunchPad – SaaS",
            thumb: "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=400&q=80",
            html: `<style>*{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif}</style>
<div style="background:#020617;color:#f8fafc;min-height:100vh">
  <nav style="padding:16px 28px;display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:18px;font-weight:800;color:#38bdf8">⚡ LaunchPad</span>
    <div style="display:flex;gap:16px;font-size:12px;color:#94a3b8;align-items:center"><span>Features</span><span>Pricing</span><span>Docs</span><span style="background:#38bdf8;color:#020617;padding:6px 14px;border-radius:6px;font-weight:700">Sign Up Free</span></div>
  </nav>
  <div style="text-align:center;padding:48px 28px">
    <span style="background:#0f172a;border:1px solid #1e293b;color:#38bdf8;font-size:11px;padding:4px 12px;border-radius:12px;letter-spacing:1px">🚀 NOW IN BETA</span>
    <h1 style="font-size:36px;font-weight:900;margin:20px 0 12px;line-height:1.2">Ship Faster.<br>Scale Smarter.</h1>
    <p style="color:#94a3b8;font-size:14px;max-width:440px;margin:0 auto 28px">The all-in-one platform for startups. Analytics, CRM, payments & automation — in one dashboard.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
      <button style="background:#38bdf8;color:#020617;border:none;padding:12px 28px;font-weight:700;border-radius:6px;cursor:pointer;font-size:14px">Start for Free</button>
      <button style="background:#0f172a;color:#f8fafc;border:1px solid #1e293b;padding:12px 28px;font-weight:600;border-radius:6px;cursor:pointer;font-size:14px">Watch Demo ▶</button>
    </div>
  </div>
  <div style="margin:0 24px;background:#0f172a;border:1px solid #1e293b;border-radius:12px;padding:20px;display:grid;grid-template-columns:repeat(3,1fr);gap:16px;text-align:center">
    <div><p style="font-size:11px;color:#64748b;letter-spacing:1px;margin-bottom:6px">ACTIVE USERS</p><p style="font-size:22px;font-weight:800;color:#38bdf8">42K+</p></div>
    <div><p style="font-size:11px;color:#64748b;letter-spacing:1px;margin-bottom:6px">UPTIME SLA</p><p style="font-size:22px;font-weight:800;color:#38bdf8">99.99%</p></div>
    <div><p style="font-size:11px;color:#64748b;letter-spacing:1px;margin-bottom:6px">G2 RATING</p><p style="font-size:22px;font-weight:800;color:#38bdf8">4.9 ⭐</p></div>
  </div>
</div>`
        }
    ]
};

function getTemplatesForType(type) {
    const map = {
        ecommerce:  templates.ecommerce,
        restaurant: templates.restaurant,
        realestate: templates.realestate,
        health:     templates.health,
        education:  templates.education,
        local:      templates.local,
        service:    templates.service,
        startup:    templates.startup,
    };
    return map[type] || templates.service;
}

function switchLanguage(lang) {
    state.currentLang = lang;
    document.body.classList.toggle('lang-ta', lang==='ta');
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if(i18n[lang][key]) el.tagName==='OPTION' ? (el.innerText=i18n[lang][key]) : (el.innerHTML=i18n[lang][key]);
    });
    document.querySelectorAll('[data-placeholder]').forEach(el => {
        const key = el.getAttribute('data-placeholder');
        if(i18n[lang][key]) el.setAttribute('placeholder', i18n[lang][key]);
    });
    const sub = document.getElementById('step-2-subtitle');
    if(state.audience==='non-tech') sub.innerText = i18n[lang].s2_sub_nontech;
    if(state.audience==='tech')     sub.innerText = i18n[lang].s2_sub_tech;
}

function goToStep(step) {
    document.querySelectorAll('.step-container').forEach(el => {
        el.classList.remove('active');
        el.style.display = 'none';
    });
    const target = document.getElementById(`step-${step}`);
    target.style.display = 'block';
    void target.offsetWidth;
    target.classList.add('active');
    document.getElementById('progress-bar').style.width = `${step * 20}%`;
    if(step === 4) confetti({ particleCount: 80, spread: 60, origin: { y: 0.6 } });
}

function setAudience(type) {
    state.audience = type;
    document.getElementById('step-2-subtitle').innerText = i18n[state.currentLang][type==='non-tech' ? 's2_sub_nontech' : 's2_sub_tech'];
    goToStep(2);
}

function generateSolution() {
    state.bizType = document.getElementById('biz-type').value;
    state.budget  = document.getElementById('budget').value;
    const t = document.getElementById('solution-title');
    const l = document.getElementById('solution-benefits');
    l.innerHTML = '';
    if(['ecommerce','restaurant'].includes(state.bizType)) {
        t.innerText = i18n[state.currentLang].sol_ecom;
        l.innerHTML = `<li><i class="fa-solid fa-check text-green-500 mr-2"></i>Online ordering & inventory</li><li><i class="fa-solid fa-check text-green-500 mr-2"></i>Secure payment gateways</li><li><i class="fa-solid fa-check text-green-500 mr-2"></i>Mobile-optimised storefront</li>`;
    } else if(['local','health'].includes(state.bizType)) {
        t.innerText = i18n[state.currentLang].sol_local;
        l.innerHTML = `<li><i class="fa-solid fa-check text-green-500 mr-2"></i>Google Maps & SEO integration</li><li><i class="fa-solid fa-check text-green-500 mr-2"></i>Easy appointment booking</li><li><i class="fa-solid fa-check text-green-500 mr-2"></i>Local review automation</li>`;
    } else {
        t.innerText = i18n[state.currentLang].sol_gen;
        l.innerHTML = `<li><i class="fa-solid fa-check text-green-500 mr-2"></i>High-converting landing pages</li><li><i class="fa-solid fa-check text-green-500 mr-2"></i>CRM & email automation</li><li><i class="fa-solid fa-check text-green-500 mr-2"></i>A/B testing & analytics</li>`;
    }
    goToStep(3);
}

function loadTemplates() {
    const type = document.getElementById('biz-type').value;
    const list = getTemplatesForType(type);
    const grid = document.getElementById('template-grid');
    grid.innerHTML = '';
    state.selectedTemplate = null;
    document.getElementById('selected-template-bar').classList.add('hidden');
    document.getElementById('preview-wrapper').classList.add('hidden');

    list.forEach((tmpl, idx) => {
        const card = document.createElement('div');
        card.className = 'template-card';
        card.id = `tcard-${idx}`;
        card.innerHTML = `
            <img src="${tmpl.thumb}" class="template-thumb" onerror="this.src='https://placehold.co/400x160/e5e7eb/6b7280?text=${encodeURIComponent(tmpl.name)}'" alt="${tmpl.name}">
            <div style="padding:10px 12px;background:#fff">
                <p style="font-size:12px;font-weight:600;color:#1f2937">${tmpl.name}</p>
                <p style="font-size:11px;color:#6b7280;margin-top:2px">Click to preview</p>
            </div>`;
        card.onclick = () => selectTemplate(idx, tmpl);
        grid.appendChild(card);
    });
    goToStep(4);
}

function selectTemplate(idx, tmpl) {
    document.querySelectorAll('.template-card').forEach(c => c.classList.remove('selected'));
    document.getElementById(`tcard-${idx}`).classList.add('selected');
    state.selectedTemplate = tmpl.name;
    document.getElementById('template-frame').srcdoc = tmpl.html;
    document.getElementById('preview-label').textContent = '🖥 Preview: ' + tmpl.name;
    document.getElementById('preview-wrapper').classList.remove('hidden');
    document.getElementById('selected-label').textContent = tmpl.name;
    document.getElementById('selected-template-bar').classList.remove('hidden');
    document.getElementById('preview-wrapper').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function closePreview() {
    document.getElementById('preview-wrapper').classList.add('hidden');
}

function submitLead(platform) {
    const name  = document.getElementById('contact-name').value.trim();
    const email = document.getElementById('contact-email').value.trim();
    const phone = document.getElementById('contact-phone').value.trim();

    ['contact-name','contact-email','contact-phone'].forEach(id => document.getElementById(id).classList.remove('error'));

    let valid = true;
    if(!name)  { document.getElementById('contact-name').classList.add('error');  valid=false; }
    if(!email) { document.getElementById('contact-email').classList.add('error'); valid=false; }
    if(!phone) { document.getElementById('contact-phone').classList.add('error'); valid=false; }
    if(!valid) return;

    document.getElementById('f-name').value     = name;
    document.getElementById('f-email').value    = email;
    document.getElementById('f-phone').value    = phone;
    document.getElementById('f-biz').value      = document.getElementById('biz-type').value;
    document.getElementById('f-budget').value   = document.getElementById('budget').value;
    document.getElementById('f-audience').value = state.audience || '';
    document.getElementById('f-template').value = state.selectedTemplate || 'Not selected';
    document.getElementById('f-platform').value = platform;

    // if(platform === 'whatsapp') {
    //     const bizText = document.getElementById('biz-type').options[document.getElementById('biz-type').selectedIndex].text;
    //     const msg = `Hi! I'm ${name}. I'm interested in a website for my ${bizText} business. Budget: ${document.getElementById('budget').value}. Template liked: ${state.selectedTemplate || 'N/A'}. Email: ${email}`;
    //     window.open(`https://wa.me/947712445678?text=${encodeURIComponent(msg)}`, '_blank');
    // }

    document.getElementById('lead-form').submit();
}
</script>
</body>
</html>