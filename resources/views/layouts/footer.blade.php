<!-- Bootstrap core JavaScript-->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/demo/datatables-demo.js') }}"></script>
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @session('success')
        <script>
            Swal.fire({
                title: "Sukses",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>
    @endsession

    @session('error')
        <script>
            Swal.fire({
                title: "Gagal",
                text: "{{ session('error') }}",
                icon: "error"
            });
        </script>
    @endsession

    @stack('scripts')

    {{-- ── Pusher & Laravel Echo via CDN ───────────────────────── --}}
    <script src="https://js.pusher.com/8.4/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    @auth
    @if(auth()->user()->role === 'admin')
    <script>
    // ── Suara notifikasi (Web Audio API) ──────────────────────────
    const notifSound = new Audio('{{ asset("sounds/notification.mp3") }}');
    notifSound.volume = 0.7;

    // ── Notifikasi panel ───────────────────────────────────────────
    let notifications = JSON.parse(localStorage.getItem('adminNotifs') || '[]');

    function renderNotifications() {
        const list  = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');
        if (!list || !badge) return;

        if (!notifications.length) {
            list.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-bell-slash d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                    <small>Belum ada notifikasi</small>
                </div>`;
            badge.classList.add('d-none');
            badge.textContent = '0';
            return;
        }

        const unread = notifications.filter(n => !n.read).length;
        if (unread > 0) {
            badge.textContent = unread > 9 ? '9+' : unread;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }

        list.innerHTML = notifications.map(function(n) {
            return `
            <a href="{{ route('booking') }}"
            onclick="markRead('${n.id}')"
            class="dropdown-item py-2 px-3 ${n.read ? '' : 'bg-light'}"
            style="border-bottom:1px solid #f1f5f9;white-space:normal;">
                <div class="d-flex align-items-start">
                    <div style="width:32px;height:32px;border-radius:50%;flex-shrink:0;margin-right:10px;
                                background:linear-gradient(135deg,#4361ee,#3a0ca3);
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-calendar-check text-white" style="font-size:.75rem;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:.8rem;font-weight:700;color:#1e293b;">
                            ${n.read ? '' : '<span style="color:#4361ee;">●</span> '}
                            ${n.user_name}
                        </div>
                        <div style="font-size:.75rem;color:#64748b;">
                            ${n.room_name} • ${n.waktu}
                        </div>
                        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">
                            ${n.tanggal} • ${n.time_ago}
                        </div>
                    </div>
                </div>
            </a>`;
        }).join('');
    }

    function addNotification(data) {
        const notif = {
            id:        data.id + '_' + Date.now(),
            user_name: data.user_name,
            room_name: data.room_name,
            tanggal:   data.tanggal,
            waktu:     data.waktu,
            keperluan: data.keperluan,
            time_ago:  'Baru saja',
            read:      false,
            created:   Date.now(),
        };
        notifications.unshift(notif);
        if (notifications.length > 20) notifications = notifications.slice(0, 20);
        localStorage.setItem('adminNotifs', JSON.stringify(notifications));
        renderNotifications();
    }

    function markRead(id) {
        notifications = notifications.map(function(n) {
            if (n.id === id) n.read = true;
            return n;
        });
        localStorage.setItem('adminNotifs', JSON.stringify(notifications));
        renderNotifications();
    }

    function clearNotifications() {
        notifications = [];
        localStorage.setItem('adminNotifs', JSON.stringify(notifications));
        renderNotifications();
    }

    function updateTimeAgo() {
        notifications = notifications.map(function(n) {
            const diff = Math.floor((Date.now() - n.created) / 60000);
            if (diff < 1)       n.time_ago = 'Baru saja';
            else if (diff < 60) n.time_ago = diff + ' menit lalu';
            else                n.time_ago = Math.floor(diff / 60) + ' jam lalu';
            return n;
        });
        localStorage.setItem('adminNotifs', JSON.stringify(notifications));
        renderNotifications();
    }

    setInterval(updateTimeAgo, 60000);

    document.addEventListener('DOMContentLoaded', function() {
        renderNotifications();
    });

    // ── Setup Echo dengan Pusher ───────────────────────────────────
    window.Echo = new Echo({
        broadcaster:  'pusher',
        key:          '{{ env("PUSHER_APP_KEY") }}',
        cluster:      '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS:     true,
    });

    window.Echo.channel('admin-dashboard')

        .listen('.booking.created', function(data) {
            // 1. Suara
            notifSound.currentTime = 0;
            notifSound.play().catch(function(e) {
                console.warn('Audio gagal:', e);
            });

            // 2. Tambah ke panel
            addNotification(data);

            // 3. Popup sekali
            Swal.fire({
                icon:              'info',
                title:             '🔔 Booking Baru Masuk!',
                html:              `
                    <div style="text-align:left;font-size:.9rem;line-height:2;">
                        <div><i class="fas fa-user" style="width:20px;color:#4361ee;"></i> <strong>${data.user_name}</strong></div>
                        <div><i class="fas fa-door-open" style="width:20px;color:#4361ee;"></i> ${data.room_name}</div>
                        <div><i class="fas fa-calendar" style="width:20px;color:#4361ee;"></i> ${data.tanggal} | ${data.waktu}</div>
                        <div><i class="fas fa-clipboard" style="width:20px;color:#4361ee;"></i> ${data.keperluan}</div>
                    </div>`,
                showConfirmButton:  true,
                confirmButtonText:  '<i class="fas fa-eye mr-1"></i> Lihat Booking',
                confirmButtonColor: '#4361ee',
                showCancelButton:   true,
                cancelButtonText:   'Nanti',
                timer:              15000,
                timerProgressBar:   true,
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("booking") }}';
                }
            });
        })

        .listen('.booking.status.updated', function(data) {
            Swal.fire({
                toast:             true,
                position:          'top-end',
                icon:              'success',
                title:             `Status booking #${data.id} diperbarui`,
                showConfirmButton:  false,
                timer:             3000,
                timerProgressBar:  true,
            });
        });
    </script>
    @endif
    @endauth
</body>

</html>