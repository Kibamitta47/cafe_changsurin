@include('components.adminmenu')

{{-- CDN ของ Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- CDN ของ Tailwind CSS (ตรวจสอบให้แน่ใจว่าโปรเจกต์ของคุณมี Tailwind) --}}
<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-slate-50 p-4 sm:p-8">
    <div class="max-w-7xl mx-auto">
        
        {{-- ส่วนหัว --}}
        <div class="mb-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">ภาพรวมระบบ</h1>
            <p class="mt-2 text-lg text-slate-500">สรุปข้อมูลสำคัญของเว็บไซต์ในที่เดียว</p>
        </div>

        {{-- ✅ ส่วนที่เพิ่มเข้ามา: การ์ดแสดงข้อมูลสรุป (Stat Cards) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- การ์ด: สมาชิกทั้งหมด --}}
            <div class="bg-white p-6 rounded-2xl shadow-md border border-slate-200 flex items-center gap-x-5">
                <div class="bg-sky-100 text-sky-600 rounded-full h-14 w-14 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <p class="text-slate-500 text-sm font-medium">สมาชิกทั้งหมด</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $totalUsers ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- การ์ด: คาเฟ่ทั้งหมด --}}
            <div class="bg-white p-6 rounded-2xl shadow-md border border-slate-200 flex items-center gap-x-5">
                <div class="bg-amber-100 text-amber-600 rounded-full h-14 w-14 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <div>
                    <p class="text-slate-500 text-sm font-medium">คาเฟ่ในระบบ</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $totalCafes ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- การ์ด: คาเฟ่รออนุมัติ --}}
            <div class="bg-white p-6 rounded-2xl shadow-md border border-slate-200 flex items-center gap-x-5">
                <div class="bg-yellow-100 text-yellow-600 rounded-full h-14 w-14 flex items-center justify-center">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-slate-500 text-sm font-medium">รอตรวจสอบ</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $pendingCafes ?? 'N/A' }}</p>
                </div>
            </div>

             {{-- การ์ด: ข่าวสารทั้งหมด --}}
            <div class="bg-white p-6 rounded-2xl shadow-md border border-slate-200 flex items-center gap-x-5">
                <div class="bg-emerald-100 text-emerald-600 rounded-full h-14 w-14 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3h3m-3 4h3m-3 4h3m-3 4h3" /></svg>
                </div>
                <div>
                    <p class="text-slate-500 text-sm font-medium">ข่าวสารทั้งหมด</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $totalNews ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- ✅ ส่วนที่ปรับปรุง: จัดวาง Layout ของกราฟใหม่ --}}
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8">
            
            {{-- กราฟคาเฟ่ยอดนิยม (ซ้าย) --}}
            <div class="xl:col-span-3 bg-white p-6 rounded-2xl shadow-md border border-slate-200">
                <h2 class="text-lg font-bold text-slate-700 mb-4">🌟 คาเฟ่ยอดนิยม (Top 10 รีวิวเฉลี่ย)</h2>
                <div class="relative h-[450px]">
                    <canvas id="topCafesChart"></canvas>
                </div>
            </div>

            {{-- กราฟผู้สมัคร และ สถานะคาเฟ่ (ขวา) --}}
            <div class="xl:col-span-2 flex flex-col gap-8">
                
                {{-- กราฟผู้สมัครสมาชิก --}}
                <div class="bg-white p-6 rounded-2xl shadow-md border border-slate-200">
                    <h2 class="text-lg font-bold text-slate-700 mb-4">📅 ผู้สมัครใหม่ (15 วันล่าสุด)</h2>
                    <div class="relative h-60">
                        <canvas id="userRegistrationChart"></canvas>
                    </div>
                </div>

                {{-- กราฟสถานะคาเฟ่ --}}
                <div class="bg-white p-6 rounded-2xl shadow-md border border-slate-200">
                    <h2 class="text-lg font-bold text-slate-700 mb-4">☕ สัดส่วนสถานะคาเฟ่</h2>
                    <div class="relative h-60 flex items-center justify-center">
                        <canvas id="cafeStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ ส่วน Script ไม่มีการเปลี่ยนแปลง Logic แค่จัดระเบียบ --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 🎨 กราฟแท่ง - ผู้สมัครสมาชิก ---
        const userCtx = document.getElementById('userRegistrationChart');
        if (userCtx) {
            new Chart(userCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'ผู้สมัคร',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 8,
                        barPercentage: 0.7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, color: '#64748b' }, grid: { color: '#e2e8f0' } },
                        x: { ticks: { color: '#64748b' }, grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // --- 🎨 กราฟโดนัท - สถานะคาเฟ่ ---
        const statusCtx = document.getElementById('cafeStatusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($cafeStatusLabels) !!},
                    datasets: [{
                        data: {!! json_encode($cafeStatusCounts) !!},
                        backgroundColor: ['#4ade80', '#facc15', '#f87171'],
                        borderColor: '#fff',
                        borderWidth: 3,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#334155', font: { size: 13, weight: '500' }, boxWidth: 12, padding: 15 } }
                    }
                }
            });
        }

        // --- 🎨 กราฟแท่งแนวนอน - คาเฟ่ยอดนิยม ---
        const topCafesCtx = document.getElementById('topCafesChart');
        if (topCafesCtx) {
            new Chart(topCafesCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($topCafeLabels) !!},
                    datasets: [{
                        label: 'คะแนนเฉลี่ย',
                        data: {!! json_encode($topCafeData) !!},
                        backgroundColor: 'rgba(168, 85, 247, 0.7)',
                        borderColor: 'rgba(168, 85, 247, 1)',
                        borderWidth: 1,
                        borderRadius: 8,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, max: 5, ticks: { color: '#475569', stepSize: 1 }, grid: { color: '#e2e8f0' } },
                        y: { ticks: { color: '#475569' }, grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>