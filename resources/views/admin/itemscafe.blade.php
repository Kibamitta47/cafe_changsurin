<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคาเฟ่ - ระบบจัดการผู้ดูแล</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .floating-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .table-row:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transform: scale(1.01);
        }
        .action-button {
            transition: all 0.3s ease;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        .modal {
            animation: modalFadeIn 0.3s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sticky-header th {
            position: sticky; top: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            z-index: 10;
        }
        .floating-card, .action-button, .table-row, .image-preview { transition: all 0.3s ease; }
        .image-preview { position: relative; }
        .image-preview:hover { transform: scale(1.1); z-index: 10; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
    @include('components.adminmenu')

    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="glass-morphism floating-card rounded-2xl shadow-2xl p-8 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-4xl font-bold gradient-text mb-2">ระบบจัดการคาเฟ่</h1>
                        <p class="text-gray-600 text-lg font-medium">แดชบอร์ดสำหรับผู้ดูแลระบบ</p>
                    </div>
                    <div class="mt-6 lg:mt-0 flex items-center bg-green-100 px-4 py-2 rounded-full">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-green-700 font-semibold">ออนไลน์</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="glass-morphism floating-card rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $approvedCount }}</div>
                    <div class="text-gray-600 mt-1">อนุมัติแล้ว</div>
                </div>
                <div class="glass-morphism floating-card rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                    <div class="text-gray-600 mt-1">รอการอนุมัติ</div>
                </div>
                <div class="glass-morphism floating-card rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $rejectedCount }}</div>
                    <div class="text-gray-600 mt-1">ปฏิเสธ</div>
                </div>
                <div class="glass-morphism floating-card rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $totalCount }}</div>
                    <div class="text-gray-600 mt-1">ทั้งหมด</div>
                </div>
            </div>
            
            @if(session('success'))
                <div id="successMessage" class="glass-morphism rounded-xl p-4 mb-6 border-l-4 border-green-500 flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <span class="text-green-700 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="glass-morphism floating-card rounded-xl p-6 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="ค้นหาคาเฟ่..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                        </div>
                        <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">ทุกสถานะ</option>
                            <option value="approved">อนุมัติแล้ว</option>
                            <option value="pending">รอการอนุมัติ</option>
                            <option value="rejected">ปฏิเสธ</option>
                        </select>
                    </div>
                    <a href="{{ route('admin.cafe.create') }}" class="action-button inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg">
                        <i class="fas fa-plus mr-2"></i>
                        เพิ่มคาเฟ่ใหม่
                    </a>
                </div>
            </div>

            <div class="glass-morphism floating-card rounded-xl shadow-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="sticky-header">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">คาเฟ่</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">เพิ่มโดย</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ที่อยู่</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="cafeTableBody" class="bg-white divide-y divide-gray-100">
                            @forelse ($cafes as $cafe)
                            <tr class="table-row" data-status="{{ $cafe->status }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if(!empty($cafe->images) && is_array($cafe->images) && isset($cafe->images[0]))
                                            <div class="relative flex-shrink-0">
                                                <img class="image-preview w-12 h-12 rounded-lg object-cover mr-4" src="{{ asset('storage/' . $cafe->images[0]) }}" alt="รูป {{ $cafe->cafe_name }}">
                                                @if(count($cafe->images) > 1)
                                                    <span class="absolute top-0 right-0 -mt-1 mr-3 bg-blue-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">+{{ count($cafe->images) - 1 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="image-preview w-12 h-12 rounded-lg mr-4 flex items-center justify-center bg-gray-200 text-gray-500 flex-shrink-0"><i class="fas fa-image"></i></div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 cafe-name">{{ $cafe->cafe_name }}</div>
                                            <div class="text-xs text-gray-600">ราคา: {{ $cafe->price_range }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = [
                                            'approved' => ['color' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'fas fa-check-circle'],
                                            'pending' => ['color' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'fas fa-clock'],
                                            'rejected' => ['color' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'fas fa-times-circle'],
                                        ];
                                        $currentStatus = $statusConfig[$cafe->status] ?? ['color' => 'bg-gray-100 text-gray-600 border-gray-300', 'icon' => 'fas fa-question-circle'];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-none border {{ $currentStatus['color'] }}">
                                        <i class="{{ $currentStatus['icon'] }} mr-1.5"></i>
                                        {{ ucfirst($cafe->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white font-bold text-lg 
                                            @if($cafe->admin) 
                                                bg-blue-600 
                                            @elseif($cafe->user) 
                                                bg-green-600 
                                            @else 
                                                bg-gray-400 
                                            @endif">
                                            @if($cafe->admin)
                                                {{ strtoupper(substr($cafe->admin->UserName ?? 'A', 0, 1)) }}
                                            @elseif($cafe->user)
                                                {{ strtoupper(substr($cafe->user->name ?? 'U', 0, 1)) }}
                                            @else
                                                ?
                                            @endif
                                        </div>
                                        <div class="ml-4 text-sm font-medium text-gray-900">
                                            @if($cafe->admin)
                                                <span class="font-semibold text-blue-800">ผู้ดูแลระบบ</span><br>
                                                <span class="text-gray-700">{{ $cafe->admin->UserName }}</span>
                                            @elseif($cafe->user)
                                                <span class="font-semibold text-green-800">ผู้ใช้ทั่วไป</span><br>
                                                <span class="text-gray-700">{{ $cafe->user->name }}</span>
                                            @else
                                                <span class="text-gray-500">ไม่มีข้อมูล</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="block font-medium">{{ $cafe->place_name }}</span>
                                    <span class="block text-xs text-gray-500">{{ $cafe->address }}</span>
                                </td>
                                
                                {{-- ** Start of New Action Buttons ** --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-2">
                                         <a href="{{ route('admin.cafe.edit', $cafe) }}" class="action-button inline-flex items-center justify-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded-lg shadow-md">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            แก้ไข
                                        </a>
                                        
                                        {{-- 2. แก้ไขฟอร์ม "ลบ" --}}
                                        <form id="deleteForm-{{ $cafe->cafe_id }}" action="{{ route('admin.cafe.destroy', $cafe) }}" method="POST" class="w-full hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" onclick="showConfirmModal('คุณแน่ใจหรือไม่ว่าต้องการลบคาเฟ่ {{ $cafe->cafe_name }}? การดำเนินการนี้ไม่สามารถย้อนกลับได้', 'delete', {{ $cafe->cafe_id }});" class="action-button inline-flex items-center justify-center w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg shadow-md">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            ลบ
                                        </button>
                                        
                                        @if($cafe->status === 'pending')
                                            {{-- 3. แก้ไขฟอร์ม "อนุมัติ" --}}
                                            <form id="approveForm-{{ $cafe->cafe_id }}" action="{{ route('admin.cafe.update_status', $cafe) }}" method="POST" class="w-full hidden">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                            </form>
                                            <button type="button" onclick="showConfirmModal('คุณแน่ใจหรือไม่ว่าต้องการอนุมัติคาเฟ่ {{ $cafe->cafe_name }}?', 'approve', {{ $cafe->cafe_id }});" class="action-button inline-flex items-center justify-center w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg shadow-md">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                อนุมัติ
                                            </button>

                                            {{-- 4. แก้ไขฟอร์ม "ปฏิเสธ" --}}
                                            <form id="rejectForm-{{ $cafe->cafe_id }}" action="{{ route('admin.cafe.update_status', $cafe) }}" method="POST" class="w-full hidden">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                            </form>
                                            <button type="button" onclick="showConfirmModal('คุณแน่ใจหรือไม่ว่าต้องการปฏิเสธคาเฟ่ {{ $cafe->cafe_name }}?', 'reject', {{ $cafe->cafe_id }});" class="action-button inline-flex items-center justify-center w-full px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm rounded-lg shadow-md">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                ปฏิเสธ
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-lg font-medium">
                                    <i class="fas fa-coffee-mug-empty text-4xl mb-4 text-gray-300"></i><br>
                                    ไม่มีข้อมูลคาเฟ่ในระบบ
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-white border-t border-gray-200">
                    {{ $cafes->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="modal glass-morphism rounded-2xl p-8 max-w-md w-full shadow-2xl">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">ยืนยันการดำเนินการ</h3>
                <p id="confirmMessage" class="text-gray-600 mb-6">คุณแน่ใจหรือไม่ว่าต้องการดำเนินการนี้?</p>
                <div class="flex space-x-4">
                    <button id="cancelBtn" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        ยกเลิก
                    </button>
                    <button id="confirmBtn" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                        ยืนยัน
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables for the modal confirmation
        let currentActionType = null;
        let currentCafeId = null;

        // Function to show the confirmation modal
        function showConfirmModal(message, actionType, cafeId) {
            currentActionType = actionType;
            currentCafeId = cafeId;

            document.getElementById('confirmMessage').textContent = message;
            document.getElementById('confirmModal').classList.remove('hidden');
            
            const confirmBtn = document.getElementById('confirmBtn');
            
            // Adjust button color based on action type
            confirmBtn.classList.remove('bg-red-600', 'bg-green-600', 'bg-gray-700', 'hover:bg-red-700', 'hover:bg-green-700', 'hover:bg-gray-800');

            if (actionType === 'delete') {
                confirmBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            } else if (actionType === 'approve') {
                confirmBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else if (actionType === 'reject') {
                confirmBtn.classList.add('bg-gray-700', 'hover:bg-gray-800');
            }
        }

        // Handle confirmation button click
        document.getElementById('confirmBtn').addEventListener('click', function() {
            if (currentActionType && currentCafeId) {
                let formId;
                if (currentActionType === 'delete') {
                    formId = 'deleteForm-' + currentCafeId;
                } else if (currentActionType === 'approve') {
                    formId = 'approveForm-' + currentCafeId;
                } else if (currentActionType === 'reject') {
                    formId = 'rejectForm-' + currentCafeId;
                }

                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            }
            document.getElementById('confirmModal').classList.add('hidden');
        });

        // Handle cancel button click
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.add('hidden');
        });

        // Close modal if the user clicks outside or presses escape
        window.onclick = function(event) {
            const modal = document.getElementById('confirmModal');
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        }
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.getElementById('confirmModal').classList.add('hidden');
            }
        });

        // Filter and Search functionality
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('statusFilter').addEventListener('change', filterTable);

        function filterTable() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let status = document.getElementById('statusFilter').value;
            let rows = document.getElementById('cafeTableBody').getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let row = rows[i];
                let cafeName = row.getElementsByClassName('cafe-name')[0].textContent.toLowerCase();
                let rowStatus = row.getAttribute('data-status');

                let matchesSearch = cafeName.includes(input);
                let matchesStatus = (status === '' || rowStatus === status);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        // Hide success message after 3 seconds
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease-out';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 3000);
    </script>
</body>
</html>