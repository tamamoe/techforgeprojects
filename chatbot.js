
const PRODUCTS = [
  // motherboards
  { id:1,  name:"MSI B650 GAMING PLUS WIFI",        category:"motherboard", price:134.49, stock:15, rating:4.5, socket:"AM5", chipset:"B650",  ddr:"DDR5", formfactor:"ATX",       wifi:true,  specs:"ATX, Socket AM5, DDR5, WiFi, Bluetooth" },
  { id:2,  name:"Gigabyte B850 A ELITE WF7",         category:"motherboard", price:219.99, stock:12, rating:4.6, socket:"AM5", chipset:"B850",  ddr:"DDR5", formfactor:"ATX",       wifi:true,  specs:"Socket AM5, DDR5, 256GB max memory, HDMI" },
  { id:3,  name:"ASUS B650E MAX GAMING WIFI W",      category:"motherboard", price:173.99, stock:18, rating:4.4, socket:"AM5", chipset:"B650E", ddr:"DDR5", formfactor:"ATX",       wifi:true,  specs:"White, Socket AM5, DDR5, 256GB max" },
  { id:4,  name:"MSI Z790 GAMING PLUS WIFI",         category:"motherboard", price:189.99, stock:10, rating:4.5, socket:"LGA1700", chipset:"Z790", ddr:"DDR5", formfactor:"ATX",    wifi:true,  specs:"ATX, Intel LGA 1700, DDR5, WiFi" },
  { id:5,  name:"Gigabyte H610M H V3 DDR4",          category:"motherboard", price:52.97,  stock:25, rating:4.2, socket:"LGA1700", chipset:"H610", ddr:"DDR4", formfactor:"mATX",   wifi:false, specs:"Supports Intel Core 14th Gen, LGA 1700, DDR4" },
  { id:6,  name:"Gigabyte B760M GAMING X AX",        category:"motherboard", price:143.84, stock:14, rating:4.3, socket:"LGA1700", chipset:"B760", ddr:"DDR5", formfactor:"mATX",   wifi:true,  specs:"Micro ATX, LGA 1700, DDR5, WiFi" },

  // cpus
  { id:7,  name:"AMD Ryzen 7 7800X3D",               category:"cpu", price:325.97, stock:8,  rating:4.8, socket:"AM5",    brand:"AMD",   cores:8,  boost:"5.0GHz",  tdp:"120W", specs:"5 GHz, Socket AM5, 120W, Ryzen 7, 3D V-Cache" },
  { id:8,  name:"AMD Ryzen 5 7600X",                 category:"cpu", price:150.80, stock:12, rating:4.6, socket:"AM5",    brand:"AMD",   cores:6,  boost:"5.3GHz",  tdp:"105W", specs:"4.7 GHz base, Socket AM5, 105W, Ryzen 5" },
  { id:9,  name:"Intel Core i9-14900K",              category:"cpu", price:404.00, stock:6,  rating:4.7, socket:"LGA1700",brand:"Intel", cores:24, boost:"6.0GHz",  tdp:"125W", specs:"3.2 GHz base / 6.0 GHz boost, LGA 1700, 125W, 24 cores" },
  { id:10, name:"Intel Core i5-14600K",              category:"cpu", price:217.62, stock:10, rating:4.5, socket:"LGA1700",brand:"Intel", cores:14, boost:"5.3GHz",  tdp:"125W", specs:"2.8 GHz base, 14 cores, LGA 1700, 125W" },
  { id:11, name:"Intel Core i7-14700KF",             category:"cpu", price:282.62, stock:7,  rating:4.6, socket:"LGA1700",brand:"Intel", cores:20, boost:"5.6GHz",  tdp:"125W", specs:"5.6 GHz boost, 20 cores, LGA 1700, 125W" },

  // gpus
  { id:12, name:"XFX Radeon RX 9060XT",              category:"gpu", price:361.92, stock:10, rating:4.5, brand:"AMD",   vram:"16GB", memtype:"GDDR6", arch:"RDNA 4",    tdp:"150W", specs:"16GB GDDR6, AMD RDNA 4, 150W" },
  { id:13, name:"PowerColor RX 7900 XTX 24GB",       category:"gpu", price:719.99, stock:5,  rating:4.7, brand:"AMD",   vram:"24GB", memtype:"GDDR6", arch:"RDNA 3",    tdp:"355W", specs:"24GB GDDR6, 4K ready" },
  { id:14, name:"PowerColor RX 6800 XT",             category:"gpu", price:940.99, stock:8,  rating:4.6, brand:"AMD",   vram:"16GB", memtype:"GDDR6", arch:"RDNA 2",    tdp:"300W", specs:"16GB GDDR6, 16 GHz memory clock" },
  { id:15, name:"ASUS Dual GeForce RTX 5060",        category:"gpu", price:274.99, stock:12, rating:4.4, brand:"NVIDIA",vram:"8GB",  memtype:"GDDR7", arch:"Blackwell", tdp:"150W", specs:"8GB GDDR7, DLSS 4, HDMI 2.1b" },
  { id:16, name:"Gigabyte GeForce RTX 5070",         category:"gpu", price:509.99, stock:6,  rating:4.8, brand:"NVIDIA",vram:"12GB", memtype:"GDDR7", arch:"Blackwell", tdp:"250W", specs:"12GB GDDR7, 28000 MHz memory" },
  { id:17, name:"Gigabyte GeForce RTX 3050",         category:"gpu", price:176.77, stock:15, rating:4.2, brand:"NVIDIA",vram:"6GB",  memtype:"GDDR6", arch:"Ampere",    tdp:"130W", specs:"6GB GDDR6, Ray Tracing, DLSS" },

  // ram
  { id:18, name:"Crucial DDR5 16GB 4800MHz",         category:"ram", price:97.99,  stock:20, rating:4.5, capacity:"16GB", speed:"4800MHz", ddr:"DDR5", formfactor:"SODIMM", cl:"CL40", specs:"Laptop SODIMM, CL40, Black" },
  { id:19, name:"Crucial DDR5 8GB 4800MHz",          category:"ram", price:34.50,  stock:25, rating:4.4, capacity:"8GB",  speed:"4800MHz", ddr:"DDR5", formfactor:"SODIMM", cl:"CL40", specs:"Laptop SODIMM, CL40, Black" },
  { id:20, name:"Acer Predator Hera 32GB",           category:"ram", price:256.99, stock:10, rating:4.7, capacity:"32GB", speed:"6800MHz", ddr:"DDR5", formfactor:"DIMM",   cl:"CL32", specs:"DDR5 RGB 6800MHz CL32, AMD EXPO compatible" },
  { id:21, name:"TEAMGROUP T-Force Delta RGB 32GB",  category:"ram", price:311.99, stock:12, rating:4.6, capacity:"32GB", speed:"6000MHz", ddr:"DDR5", formfactor:"DIMM",   cl:"CL40", specs:"DDR5 6000MHz CL40, Desktop, Black" },
  { id:22, name:"Kingston FURY Renegade 64GB",       category:"ram", price:316.99, stock:8,  rating:4.8, capacity:"64GB", speed:"6400MHz", ddr:"DDR5", formfactor:"DIMM",   cl:"CL32", specs:"DDR5 RGB 6400MT/s CL32, Silver/Black" },
  { id:23, name:"Corsair VENGEANCE RGB 64GB",        category:"ram", price:349.99, stock:7,  rating:4.7, capacity:"64GB", speed:"7000MHz", ddr:"DDR5", formfactor:"DIMM",   cl:"CL40", specs:"DDR5 7000MHz CL40, XMP 3.0" },

  // storage
  { id:24, name:"fanxiang M.2 SSD 256GB",            category:"storage", price:30.55,  stock:30, rating:4.3, capacity:"256GB", gen:"Gen3", read:"3200MB/s",  write:"1800MB/s", specs:"NVMe PCIe Gen3x4, up to 3200MB/s" },
  { id:25, name:"BIWIN Black Opal NV3500 512GB",     category:"storage", price:42.99,  stock:25, rating:4.4, capacity:"512GB", gen:"Gen3", read:"3500MB/s",  write:"2800MB/s", specs:"PCIe Gen3x4 NVMe, 3500MB/s read" },
  { id:26, name:"Acer Predator GM9 1TB",             category:"storage", price:119.99, stock:15, rating:4.8, capacity:"1TB",   gen:"Gen5", read:"14000MB/s", write:"12000MB/s",specs:"Gen5 SSD, up to 14000MB/s, NVMe 2.0" },
  { id:27, name:"Crucial P310 1TB",                  category:"storage", price:74.99,  stock:20, rating:4.6, capacity:"1TB",   gen:"Gen4", read:"7100MB/s",  write:"6000MB/s", specs:"M.2 NVMe PCIe Gen4, 7100MB/s" },
  { id:28, name:"Lexar EQ790 2TB",                   category:"storage", price:146.99, stock:12, rating:4.5, capacity:"2TB",   gen:"Gen4", read:"7000MB/s",  write:"6500MB/s", specs:"M.2 PCIe Gen4x4, up to 7000MB/s" },
  { id:29, name:"WD Black SN850X 4TB",               category:"storage", price:301.23, stock:8,  rating:4.9, capacity:"4TB",   gen:"Gen4", read:"7300MB/s",  write:"6600MB/s", specs:"M.2 PCIe Gen4 x4 NVMe" },
];

// in order to not have an actual ai but a semblance of one, just referring to ELIZA principles.
const REFUSAL = "I'm sorry, I cannot help you with that query.";

// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/match thanks mozilla
const OFF_TOPIC_PATTERNS = [
  /\bweather\b/,/\bpolitics?\b/,/\bsport(s)?\b/,/\bjoke[s]?\b/,/\bmovie[s]?\b/,
  /\brecipe[s]?\b/,/\bcode\s+for\b/,/\bhow\s+to\s+make\b/,/\bwrite\s+(me\s+)?(an?|the)\b/,
  /\btell\s+me\s+about\s+(life|love|death|war)\b/,/\bignore\s+(previous|all)\b/,
  /\bpretend\s+(you|to)\b/,/\byou\s+are\s+now\b/,/\bdan\b/,/\bjailbreak\b/,
  /\bact\s+as\b/,/\bnew\s+instructions?\b/,/\bsystem\s+prompt\b/,/\bforget\s+everything\b/,
  /\bwho\s+(made|created|built|are)\s+you\b/,/\banthrop(ic)?\b/,/\bclau(de)?\b/,
  /\bwhat\s+is\s+(your|the)\s+(purpose|goal|ai|model)\b/,
];

function normalise(str) { return str.toLowerCase().replace(/[£$,!?]/g, "").trim(); }

function findProducts(input) {
  const q = normalise(input);
  return PRODUCTS.filter(p => {
    const name = normalise(p.name); const tokens = name.split(/\s+/).filter(t => t.length > 2);
    const hits = tokens.filter(t => q.includes(t)); return hits.length >= 2 || q.includes(name); }); }

function formatProduct(p) {
  const stockLabel = p.stock === 0 ? "❌ Out of stock"
    : p.stock <= 5 ? `⚠️ Low stock (${p.stock} left)`
    : `✅ In stock (${p.stock} units)`;
  return `<div class="chat-product-card"> <strong>${p.name}</strong>
    <span class="chat-tag chat-tag-${p.category}">${p.category.toUpperCase()}</span><br>
    <span class="chat-price">£${p.price.toFixed(2)}</span> &nbsp;⭐ ${p.rating}/5 &nbsp; ${stockLabel}<br>
    <em>${p.specs}</em> </div>`; }

function compareProducts(a, b) {
  const rows = [
    ["Price",    `£${a.price.toFixed(2)}`, `£${b.price.toFixed(2)}`],
    ["Rating",   `⭐ ${a.rating}/5`,        `⭐ ${b.rating}/5`],
    ["Stock",    `${a.stock} units`,         `${b.stock} units`],
    ["Category", a.category.toUpperCase(),   b.category.toUpperCase()],
    ["Specs",    a.specs,                    b.specs],
  ];

  const winner = a.rating > b.rating ? a.name : b.rating > a.rating ? b.name : "Both are equally rated";

  return `<table class="chat-compare-table">
    <thead><tr><th>Feature</th><th>${a.name}</th><th>${b.name}</th></tr></thead>
    <tbody>${rows.map(([f,av,bv]) => `<tr><td><b>${f}</b></td><td>${av}</td><td>${bv}</td></tr>`).join("")}</tbody>
  </table>
  <div class="chat-verdict">🏆 <strong>Recommendation:</strong> ${winner === "Both are equally rated" ? "Both are equally rated — choose by price or specs preference." : `<em>${winner}</em> edges ahead on rating.`}</div>`;
}


//  intent checks thanks mozilla https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Regular_expressions
function respond(input) {
  const q = normalise(input);

  // refusal
  for (const pattern of OFF_TOPIC_PATTERNS) { if (pattern.test(q)) return REFUSAL; }

  // 2. hi
  if (/^(hi|hello|hey|good\s*(morning|afternoon|evening)|howdy|sup|yo)\b/.test(q)) {
    return "Hey there! 👋 I can help you find and compare products in our catalog — motherboards, CPUs, GPUs, RAM, and storage. What are you looking for?";
  }

  // 3. help
  if (/\b(help|what can you|what do you|commands?|options?)\b/.test(q)) {
    return `Here's what I can help with:<br><br>
• Look up any product by name<br>
• Browse by category: <b>motherboards, CPUs, GPUs, RAM, storage</b><br>
• Compare two products (e.g. <em>"compare RTX 5070 vs RX 7900 XTX"</em>)<br>
• Filter by budget (e.g. <em>"GPUs under £300"</em>)<br>
• Find best-rated or cheapest items in a category<br>
• Check stock and compatibility`;
  }

  // 4. compare products yummers, https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Regular_expressions/Groups_and_backreferences
  const vsMatch = q.match(/compare\s+(.+?)\s+(?:vs?\.?|and|versus|against|with)\s+(.+)/);
  const vsMatch2 = q.match(/(.+?)\s+(?:vs?\.?|versus|against)\s+(.+)/);
  const matchPair = vsMatch || vsMatch2;

  if (matchPair) {
    const termA = matchPair[1].trim();
    const termB = matchPair[2].trim();
    const resultsA = PRODUCTS.filter(p => normalise(p.name).includes(termA) || termA.split(" ").filter(t=>t.length>2).every(t => normalise(p.name).includes(t)));
    const resultsB = PRODUCTS.filter(p => normalise(p.name).includes(termB) || termB.split(" ").filter(t=>t.length>2).every(t => normalise(p.name).includes(t)));

    if (resultsA.length && resultsB.length) {
      return `<strong>Comparing:</strong><br>${compareProducts(resultsA[0], resultsB[0])}`;
    }
    if (!resultsA.length && !resultsB.length) return `I couldn't find either product in our catalog. Try using part of the product name, e.g. <em>"RTX 5070"</em> or <em>"7800X3D"</em>.`;
    if (!resultsA.length) return `I found <strong>${resultsB[0].name}</strong> but couldn't match the first product. Try rephrasing.`;
    return `I found <strong>${resultsA[0].name}</strong> but couldn't match the second product. Try rephrasing.`;
  }

  // 5. categories
  const catMap = { motherboard:["motherboard","mobo","mainboard"], cpu:["cpu","processor","ryzen","core i"], gpu:["gpu","graphics card","video card","rtx","rx ","radeon","geforce"], ram:["ram","memory","ddr5","ddr4"], storage:["ssd","storage","nvme","hard drive","m.2","hdd"] };
  let detectedCat = null;
  for (const [cat, keys] of Object.entries(catMap)) {
    if (keys.some(k => q.includes(k))) { detectedCat = cat; break; }
  }

  // below money
  const budgetMatch = q.match(/under\s+[£$]?(\d+)/);
  const budget = budgetMatch ? parseFloat(budgetMatch[1]) : null;

  // rated + money
  const wantsBest    = /\b(best|top|recommended?|highest rated|great)\b/.test(q);
  const wantsCheap   = /\b(cheap(est)?|lowest price|budget|affordable|value)\b/.test(q);
  const wantsStock   = /\b(in stock|available)\b/.test(q);

  if (detectedCat) {
    let pool = PRODUCTS.filter(p => p.category === detectedCat);
    if (budget)      pool = pool.filter(p => p.price <= budget);
    if (wantsStock)  pool = pool.filter(p => p.stock > 0);
    if (wantsCheap)  pool.sort((a,b) => a.price - b.price);
    else if (wantsBest) pool.sort((a,b) => b.rating - a.rating);

    if (!pool.length) return `No ${detectedCat.toUpperCase()} products match that filter. Try a higher budget or browse all ${detectedCat}s.`;

    const intro = budget ? `Here are our <strong>${detectedCat.toUpperCase()}</strong> options under <strong>£${budget}</strong>:`
      : wantsBest ? `Here are our top-rated <strong>${detectedCat.toUpperCase()}</strong> options:`
      : wantsCheap ? `Here are our most affordable <strong>${detectedCat.toUpperCase()}</strong> options:`
      : `Here are all our <strong>${detectedCat.toUpperCase()}</strong> options:`;
    return intro + "<br><br>" + pool.map(formatProduct).join("");
  }

  // keyword filtering
  const matches = findProducts(input);
  if (matches.length === 1) return `Here's what I found:<br><br>${formatProduct(matches[0])}`;
  if (matches.length > 1 && matches.length <= 5) return `I found a few matches:<br><br>${matches.map(formatProduct).join("")}`;
  if (matches.length > 5) return `I found ${matches.length} products matching that. Try being more specific — e.g. include the brand or model number.`;

  // stocks
  if (/\b(stock|available|left|have you got)\b/.test(q)) {
    const lowStock = PRODUCTS.filter(p => p.stock > 0 && p.stock <= 5);
    return `Here are items with <strong>low stock</strong> right now:<br><br>${lowStock.map(formatProduct).join("")}`;
  }

  // price range
  const rangeMatch = q.match(/between\s+[£$]?(\d+)\s+and\s+[£$]?(\d+)/);
  if (rangeMatch) {
    const lo = parseFloat(rangeMatch[1]), hi = parseFloat(rangeMatch[2]);
    const pool = PRODUCTS.filter(p => p.price >= lo && p.price <= hi);
    if (!pool.length) return `No products found between £${lo} and £${hi}.`;
    return `Products between <strong>£${lo}–£${hi}</strong>:<br><br>${pool.map(formatProduct).join("")}`;
  }

  // socketing GAH
  const socketMatch = q.match(/\b(am5|lga\s*1700|lga1700|lga\s*1200|lga1200)\b/);
  if (socketMatch && /\b(compatible|fit|work with|motherboard|cpu|processor)\b/.test(q)) {
    const socket = socketMatch[1].replace(/\s/,"").toUpperCase();
    const boards = PRODUCTS.filter(p => p.category === "motherboard" && p.socket && normalise(p.socket) === normalise(socket));
    const cpus   = PRODUCTS.filter(p => p.category === "cpu" && p.socket && normalise(p.socket) === normalise(socket));
    if (!boards.length && !cpus.length) return `No products found for socket ${socket} in our catalog.`;
    let out = `<strong>Products compatible with ${socket}:</strong><br><br>`;
    if (cpus.length)   out += `<em>CPUs:</em><br>${cpus.map(formatProduct).join("")}`;
    if (boards.length) out += `<em>Motherboards:</em><br>${boards.map(formatProduct).join("")}`;
    return out;
  }

  // fallback
  return "I didn't quite catch that. I can help with product lookups, comparisons, and recommendations from our catalog. Try asking something like:<br><br>• <em>\"Show me all GPUs under £400\"</em><br>• <em>\"Compare Ryzen 7 7800X3D vs i9-14900K\"</em><br>• <em>\"Best rated storage drive\"</em>";
}


// thanks  https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener
(function () {
  // Inject HTML widget into page
  const container = document.createElement("div");
  container.innerHTML = `
    <div id="cb-widget">
      <button id="cb-toggle" aria-label="Toggle chat">
        <span id="cb-toggle-open"><i class="fas fa-comments"></i></span>
        <span id="cb-toggle-close" style="display:none"><i class="fas fa-times"></i></span>
      </button>
      <div id="cb-window" aria-live="polite">
        <div id="cb-header">
          <i class="fas fa-microchip"></i>
          <div>
            <div id="cb-title">PC Hardware Assistant</div>
            <div id="cb-subtitle">PRODUCT QUERIES ONLY</div>
          </div>
          <span id="cb-status-dot"></span>
        </div>
        <div id="cb-messages">
          <div class="cb-msg cb-bot">
            <div class="cb-avatar"><i class="fas fa-robot"></i></div>
            <div class="cb-bubble">Hey! 👋 Ask me anything about our <strong>motherboards, CPUs, GPUs, RAM, or storage</strong>. I can compare products, check stock, and filter by budget.</div>
          </div>
        </div>
        <div id="cb-chips"></div>
        <div id="cb-input-area">
          <textarea id="cb-input" rows="1" placeholder="Ask about any product…" aria-label="Chat input"></textarea>
          <button id="cb-send" aria-label="Send"><i class="fas fa-paper-plane"></i></button>
        </div>
      </div>
    </div>`;
  document.body.appendChild(container);

  // immediate responses
  const CHIPS = [
    "Best GPU under £400", "Compare RTX 5070 vs RX 7900 XTX",
    "Cheapest motherboard", "Ryzen 7 7800X3D specs",
    "AM5 compatible parts", "Fastest storage drive",
  ];
  const chipsEl = document.getElementById("cb-chips");
  CHIPS.forEach(text => {
    const btn = document.createElement("button");
    btn.className = "cb-chip";
    btn.textContent = text;
    btn.addEventListener("click", () => sendMessage(text)); // MDN EventTarget.addEventListener
    chipsEl.appendChild(btn);
  });

  const messagesEl  = document.getElementById("cb-messages");
  const inputEl     = document.getElementById("cb-input");
  const sendBtn     = document.getElementById("cb-send");
  const toggleBtn   = document.getElementById("cb-toggle");
  const windowEl    = document.getElementById("cb-window");
  const toggleOpen  = document.getElementById("cb-toggle-open");
  const toggleClose = document.getElementById("cb-toggle-close");
  let firstMessage  = true;

  // fancy thing
  function appendMessage(html, role) {
    const wrap = document.createElement("div");
    wrap.className = `cb-msg cb-${role}`;
    if (role === "bot") {
      wrap.innerHTML = `<div class="cb-avatar"><i class="fas fa-robot"></i></div><div class="cb-bubble">${html}</div>`;
    } else {
      wrap.innerHTML = `<div class="cb-bubble">${html}</div>`;
    }
    messagesEl.appendChild(wrap);
    // reference: https://developer.mozilla.org/en-US/docs/Web/API/Element/scrollIntoView
    wrap.scrollIntoView({ behavior: "smooth", block: "end" });
  }

  // more fancy
  function showTyping() {
    const el = document.createElement("div");
    el.className = "cb-msg cb-bot cb-typing-row";
    el.id = "cb-typing";
    el.innerHTML = `<div class="cb-avatar"><i class="fas fa-robot"></i></div><div class="cb-bubble cb-typing"><span></span><span></span><span></span></div>`;
    messagesEl.appendChild(el);
    el.scrollIntoView({ behavior: "smooth", block: "end" });
  }
  function hideTyping() {
    document.getElementById("cb-typing")?.remove();
  }

  function sendMessage(overrideText) {
    const text = (overrideText || inputEl.value).trim();
    if (!text) return;
    inputEl.value = "";

    if (firstMessage) { chipsEl.style.display = "none"; firstMessage = false; }

    appendMessage(escapeHtml(text), "user");

    // i guess this is needed? i mean like its seen everywhere https://developer.mozilla.org/en-US/docs/Web/API/setTimeout)
    showTyping();
    setTimeout(() => {
      hideTyping();
      const reply = respond(text);
      appendMessage(reply, "bot");
    }, 420);
  }

  // no outside scripting 
  function escapeHtml(str) {
    return str.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");
  }

  sendBtn.addEventListener("click", () => sendMessage());
  inputEl.addEventListener("keydown", e => {
    if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); sendMessage(); }
  });
  // resize https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/style
  inputEl.addEventListener("input", () => {
    inputEl.style.height = "auto";
    inputEl.style.height = Math.min(inputEl.scrollHeight, 80) + "px";
  });

  toggleBtn.addEventListener("click", () => {
    const isOpen = windowEl.style.display !== "none" && windowEl.style.display !== "";
    windowEl.style.display = isOpen ? "none" : "flex";
    toggleOpen.style.display  = isOpen ? "inline" : "none";
    toggleClose.style.display = isOpen ? "none" : "inline";
  });
  // closed
  windowEl.style.display = "none";
})();
