/**
 * Notifications Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('notification-wrapper');
    const btn = document.getElementById('notification-btn');
    const dropdown = document.getElementById('notification-dropdown');
    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    const unreadCountBadge = document.getElementById('unread-count-badge');

    if (!btn || !dropdown) return;

    // Toggle Dropdown
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            fetchNotifications();
        }
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Fetch Notifications
    async function fetchNotifications() {
        try {
            const response = await fetch('/vasmat/includes/notification_api.php?action=fetch');
            const data = await response.json();
            
            renderNotifications(data.notifications);
            updateBadges(data.unread_count);
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }

    function renderNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            list.innerHTML = `
                <div class="p-10 text-center text-slate-400">
                    <i class="fas fa-bell-slash text-2xl mb-3 opacity-20"></i>
                    <p class="text-[10px] font-bold uppercase tracking-widest">No notifications</p>
                </div>
            `;
            return;
        }

        list.innerHTML = notifications.map(n => `
            <div class="p-5 border-b border-slate-50 hover:bg-slate-50 transition-all group relative">
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-xl ${n.is_read == 1 ? 'bg-slate-100 text-slate-400' : 'bg-primary-50 text-primary-500'} flex items-center justify-center flex-shrink-0 transition-colors">
                        <i class="fas fa-info-circle text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="text-xs font-black text-slate-900 leading-tight">${n.title}</h4>
                            <button onclick="deleteNotification(event, ${n.id})" class="text-slate-300 hover:text-rose-500 transition-colors opacity-0 group-hover:opacity-100">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed mb-2">${n.message}</p>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">${timeAgo(n.created_at)}</span>
                    </div>
                </div>
                ${n.is_read == 0 ? '<span class="absolute top-5 right-5 w-1.5 h-1.5 bg-primary-500 rounded-full"></span>' : ''}
            </div>
        `).join('');
    }

    function updateBadges(count) {
        if (count > 0) {
            badge.classList.remove('hidden');
            unreadCountBadge.innerText = `${count} New`;
            unreadCountBadge.classList.remove('bg-slate-100', 'text-slate-400');
            unreadCountBadge.classList.add('bg-rose-50', 'text-rose-500');
        } else {
            badge.classList.add('hidden');
            unreadCountBadge.innerText = `0 New`;
            unreadCountBadge.classList.remove('bg-rose-50', 'text-rose-500');
            unreadCountBadge.classList.add('bg-slate-100', 'text-slate-400');
        }
    }

    // Initial check
    fetchNotifications();

    // Poll every 60 seconds
    setInterval(fetchNotifications, 60000);
});

async function markAllRead() {
    try {
        await fetch('/vasmat/includes/notification_api.php?action=mark_read');
        // Refresh
        location.reload(); 
    } catch (error) {
        console.error('Error marking read:', error);
    }
}

async function deleteNotification(e, id) {
    e.stopPropagation();
    if(!confirm('Delete this notification?')) return;
    
    try {
        await fetch(`/vasmat/includes/notification_api.php?action=delete&id=${id}`);
        // Refresh list
        const wrapper = document.getElementById('notification-wrapper');
        const event = new CustomEvent('refreshNotifications');
        document.dispatchEvent(new Event('DOMContentLoaded')); // Dirty way to re-init or just call fetch
        location.reload(); // Simplest for now
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
}

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    let interval = Math.floor(seconds / 31536000);
    if (interval > 1) return interval + " years ago";
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) return interval + " months ago";
    interval = Math.floor(seconds / 86400);
    if (interval > 1) return interval + " days ago";
    interval = Math.floor(seconds / 3600);
    if (interval > 1) return interval + " hours ago";
    interval = Math.floor(seconds / 60);
    if (interval > 1) return interval + " minutes ago";
    return "just now";
}
