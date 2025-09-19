<footer class="bg-slate-900 text-white py-10 mt-8">
  <div class="max-w-7xl mx-auto px-6">
    <!-- ============ Mobile (Accordion) ============ -->
    <div class="md:hidden space-y-4">
      <!-- Brand -->
      <div>
        <h4 class="text-xl font-bold mb-2 text-cyan-400">น้องช้างสะเร็น</h4>
        <p class="text-slate-400 text-sm leading-relaxed">
          แพลตฟอร์มค้นหาข่าวสารและคาเฟ่ในจังหวัดสุรินทร์
        </p>
      </div>

      <!-- หมวดหมู่ -->
      <details class="group rounded-xl border border-slate-800 bg-slate-950/40">
        <summary class="flex items-center justify-between cursor-pointer px-4 py-3">
          <span class="text-base font-semibold text-slate-200">หมวดหมู่</span>
          <span class="i flex items-center justify-center rounded-full w-7 h-7 bg-slate-800/60">
            <i class="fa-solid fa-chevron-down transition-transform duration-200 group-open:rotate-180"></i>
          </span>
        </summary>
        <div class="px-4 pb-4">
          <ul class="space-y-2 text-slate-400 text-sm">
            <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition">ข่าวสาร</a></li>
            <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition">คาเฟ่</a></li>
            <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition">โปรโมชั่น</a></li>
          </ul>
        </div>
      </details>

      <!-- ลิงก์ด่วน -->
      <details class="group rounded-xl border border-slate-800 bg-slate-950/40">
        <summary class="flex items-center justify-between cursor-pointer px-4 py-3">
          <span class="text-base font-semibold text-slate-200">ลิงก์ด่วน</span>
          <span class="i flex items-center justify-center rounded-full w-7 h-7 bg-slate-800/60">
            <i class="fa-solid fa-chevron-down transition-transform duration-200 group-open:rotate-180"></i>
          </span>
        </summary>
        <div class="px-4 pb-4">
          <ul class="space-y-2 text-slate-400 text-sm">
            <li><a href="{{ route('about.us') }}" class="hover:text-cyan-300 transition">เกี่ยวกับเรา</a></li>
            <li><a href="{{ route('problem.info') }}" class="hover:text-cyan-300 transition">แจ้งปัญหา</a></li>
            <li><a href="{{ route('advertising.packages') }}" class="hover:text-cyan-300 transition">ติดต่อโฆษณา</a></li>
            <li><a href="{{ url('/login-admin') }}" class="hover:text-cyan-300 transition">สำหรับ Admin Login</a></li>
          </ul>
        </div>
      </details>

      <!-- ติดตามเรา -->
      <details class="group rounded-xl border border-slate-800 bg-slate-950/40">
        <summary class="flex items-center justify-between cursor-pointer px-4 py-3">
          <span class="text-base font-semibold text-slate-200">ติดตามเรา</span>
          <span class="i flex items-center justify-center rounded-full w-7 h-7 bg-slate-800/60">
            <i class="fa-solid fa-chevron-down transition-transform duration-200 group-open:rotate-180"></i>
          </span>
        </summary>
        <div class="px-4 pb-4 space-y-3">
          <div class="flex items-center space-x-4">
            <a href="#" aria-label="Facebook">
              <i class="fab fa-facebook-f text-2xl" style="color:#1877F2;"></i>
            </a>
            <a href="https://line.me/ti/p/@363tvzhr" target="_blank" rel="noopener" aria-label="Line">
              <i class="fab fa-line text-2xl" style="color:#00C300;"></i>
            </a>
          </div>
          <p class="text-slate-400 text-sm font-medium">
            ติดต่อ: <span class="block">nongchangsaren@gmail.com</span>
          </p>
          <p class="text-slate-400 text-sm">โทรศัพท์: 08-XXXX-XXXX</p>
        </div>
      </details>
    </div>

    <!-- ============ Desktop / Tablet (4-cols) ============ -->
    <div class="hidden md:grid grid-cols-4 gap-10">
      <div>
        <h4 class="text-2xl font-bold mb-4 text-cyan-400">น้องช้างสะเร็น</h4>
        <p class="text-slate-400 text-sm leading-relaxed">
          แพลตฟอร์มค้นหาข่าวสารและคาเฟ่ในจังหวัดสุรินทร์
        </p>
      </div>

      <div>
        <h5 class="text-lg font-semibold mb-4 text-slate-200">หมวดหมู่</h5>
        <ul class="space-y-3 text-slate-400 text-sm">
          <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">ข่าวสาร</a></li>
          <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">คาเฟ่</a></li>
          <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">โปรโมชั่น</a></li>
        </ul>
      </div>

      <div>
        <h5 class="text-lg font-semibold mb-4 text-slate-200">ลิงก์ด่วน</h5>
        <ul class="space-y-3 text-slate-400 text-sm">
          <li><a href="{{ route('about.us') }}" class="hover:text-cyan-300 transition-colors">เกี่ยวกับเรา</a></li>
          <li><a href="{{ route('problem.info') }}" class="hover:text-cyan-300 transition-colors">แจ้งปัญหา</a></li>
          <li><a href="{{ route('advertising.packages') }}" class="hover:text-cyan-300 transition-colors">ติดต่อโฆษณา</a></li>
          <li><a href="{{ url('/login-admin') }}" class="hover:text-cyan-300 transition-colors">สำหรับ Admin Login</a></li>
        </ul>
      </div>

      <div>
        <h5 class="text-lg font-semibold mb-4 text-slate-200">ติดตามเรา</h5>
        <div class="flex items-center space-x-4 mb-3">
          <a href="#" aria-label="Facebook">
            <i class="fab fa-facebook-f text-2xl" style="color:#1877F2;"></i>
          </a>
          <a href="https://line.me/ti/p/@363tvzhr" target="_blank" rel="noopener" aria-label="Line">
            <i class="fab fa-line text-2xl" style="color:#00C300;"></i>
          </a>
        </div>
        <p class="text-slate-400 text-sm font-medium">ติดต่อ: <span class="block">nongchangsaren@gmail.com</span></p>
        <p class="text-slate-400 text-sm">โทรศัพท์: 08-XXXX-XXXX</p>
      </div>
    </div>
  </div>

  <!-- Copyright -->
  <div class="border-t border-slate-800 mt-10 pt-6 text-center text-slate-500 text-xs sm:text-sm">
    © {{ now()->year }} น้องช้างสะเร็น. สงวนลิขสิทธิ์
  </div>
</footer>
