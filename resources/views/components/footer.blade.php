<footer class="bg-slate-900 text-white py-6 mt-6">
  <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-6">
    <div>
      <h4 class="text-xl font-bold mb-2 text-cyan-400">น้องช้างสะเร็น</h4>
      <p class="text-slate-400 text-xs">แพลตฟอร์มค้นหาข่าวสารและคาเฟ่ในจังหวัดสุรินทร์</p>
    </div>

    <div>
      <h5 class="text-base font-semibold mb-2 text-slate-200">หมวดหมู่</h5>
      <ul class="space-y-1.5 text-slate-400 text-xs">
        <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">ข่าวสาร</a></li>
        <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">คาเฟ่</a></li>
        <li><a href="{{ route('welcome') }}" class="hover:text-cyan-300 transition-colors">โปรโมชั่น</a></li>
      </ul>
    </div>

    <div>
      <h5 class="text-base font-semibold mb-2 text-slate-200">ลิงก์ด่วน</h5>
      <ul class="space-y-1.5 text-slate-400 text-xs">
        <li><a href="{{ route('about.us') }}" class="hover:text-cyan-300 transition-colors">เกี่ยวกับเรา</a></li>
        <li><a href="{{ route('problem.info') }}" class="hover:text-cyan-300 transition-colors">แจ้งปัญหา</a></li>
        <li><a href="{{ route('advertising.packages') }}" class="hover:text-cyan-300 transition-colors">ติดต่อโฆษณา</a></li>
        <li><a href="{{ url('/login-admin') }}" class="hover:text-cyan-300 transition-colors">สำหรับ Admin Login</a></li>
      </ul>
    </div>

    <div>
      <h5 class="text-base font-semibold mb-2 text-slate-200">ติดตามเรา</h5>
      <div class="flex flex-col space-y-1.5 text-slate-400 text-xs">
        <div class="flex space-x-3">
          <a href="#" aria-label="Facebook">
            <i class="fab fa-facebook-f text-lg" style="color:#1877F2;"></i>
          </a>
          <a href="https://line.me/ti/p/@363tvzhr" target="_blank" aria-label="Line">
            <i class="fab fa-line text-lg" style="color:#00C300;"></i>
          </a>
        </div>
        <p class="font-medium">ติดต่อ: nongchangsaren@gmail.com</p>
        <p>โทรศัพท์: 08-XXXX-XXXX</p>
      </div>
    </div>
  </div>

  <div class="border-t border-slate-800 mt-6 pt-4 text-center text-slate-500 text-xs">
    © {{ now()->year }} น้องช้างสะเร็น. สงวนลิขสิทธิ์
  </div>
</footer>
