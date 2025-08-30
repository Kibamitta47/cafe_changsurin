<footer class="bg-slate-900 text-white py-12 mt-8">
  <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
    <div>
      <h4 class="text-2xl font-bold mb-4 text-cyan-400">น้องช้างสะเร็น</h4>
      <p class="text-slate-400 text-sm">แพลตฟอร์มค้นหาข่าวสารและคาเฟ่ในจังหวัดสุรินทร์</p>
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
      <div class="flex flex-col space-y-2 text-slate-400">
        <div class="flex space-x-4">
          <a href="#" class="text-black">
            <i class="fab fa-facebook-f text-2xl" style="color:#1877F2;"></i>
          </a>
          <a href="https://line.me/ti/p/@363tvzhr" class="text-black" target="_blank">
            <i class="fab fa-line text-2xl" style="color:#00C300;"></i>
          </a>
        </div>
        <p class="font-semibold">ติดต่อ: nongchangsaren@gmail.com</p>
        <p>โทรศัพท์: 08-XXXX-XXXX</p>
      </div>
    </div>
  </div>

  <div class="border-t border-slate-800 mt-12 pt-8 text-center text-slate-500 text-sm">
    © {{ now()->year }} น้องช้างสะเร็น. สงวนลิขสิทธิ์
  </div>
</footer>
