      <aside class="w-full md:w-64 bg-[#2C3E50] text-white flex flex-col justify-between shadow-lg">
            <div>
                <div class="p-4 flex items-center justify-center space-x-3 border-b border-[#1F2A36]">
                    <img src="{{ asset('assets/images/Logo_PLN.png') }}" alt="Logo PLN" class="w-12 h-12 object-contain" />
                    <div>
                        <p class="text-base font-bold leading-5">PLN</p>
                        <p class="text-xs text-gray-300">UID KALSELTENG</p>
                    </div>
                </div>

                <nav class="mt-6 px-4 space-y-2">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#FFD100] text-black' : 'hover:bg-[#3C5A6D]' }}">
                        <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                    </a>

                    <div class="relative">
                        <button onclick="toggleDropdown('attendanceDropdown')"
                            class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 {{ request()->routeIs('attendance.*') ? 'bg-[#3C5A6D]' : 'hover:bg-[#3C5A6D]' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-user-check"></i> <span>Attendance</span>
                            </div>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div id="attendanceDropdown"
                            class="{{ request()->routeIs('attendance.*') ? 'mt-2 space-y-1 rounded-xl bg-[#34495E] overflow-hidden transition-all' : 'hidden mt-2 space-y-1 rounded-xl bg-[#34495E] overflow-hidden transition-all' }}">
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">History</a>
                            <a href="{{ route('attendance.my') }}"
                                class="block px-6 py-2 text-sm {{ request()->routeIs('attendance.my') ? 'bg-[#2C3E50] text-[#FFD100]' : 'hover:bg-[#2C3E50] hover:text-[#FFD100]' }}">
                                My Attendance
                            </a>
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">Attendance
                                Records</a>
                        </div>
                    </div>

                    <div class="relative">
                        <button onclick="toggleDropdown('approvalDropdown')"
                            class="flex items-center justify-between w-full px-4 py-2 rounded-xl transition duration-150 hover:bg-[#3C5A6D]">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-thumbs-up"></i> <span>Approval</span>
                            </div>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div id="approvalDropdown"
                            class="hidden mt-2 space-y-1 rounded-xl bg-[#34495E] overflow-hidden transition-all">
                            <a href="#"
                                class="block px-6 py-2 text-sm hover:bg-[#2C3E50] hover:text-[#FFD100]">Attendance
                                Approval</a>
                        </div>
                    </div>

                    <a href="#"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl transition duration-150 hover:bg-[#3C5A6D]">
                        <i class="fa-solid fa-cog"></i> <span>Settings</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-sm transition duration-150 hover:bg-[#3C5A6D]">
                            <i class="fa-solid fa-right-from-bracket"></i> <span>Log Out</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>