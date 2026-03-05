<?php
$this->title = 'Profile';
?>

<div class="grid-2">
    <!-- Personal Information -->
    <div class="card" style="background-color: #0F1C32;">
        <div class="card-title" style="color:white"><span>👤</span> Personal Information</div>
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
            <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,var(--accent),var(--purple));display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;color:white"><img src="/assets/profile.jpeg" style="width:100%; height:100%; border-radius:16px; object-fit:cover;"></div>
            <div>
                <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700; color:white;">Bwesigye Treasure</div>
                <div style="color:var(--text3);font-size:13px">treasurebwesigye@gmail.com</div>
                <span class="pill pill-gold" style="margin-top:6px;display:inline-flex">⭐ Premium</span>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>First Name</label>
                <input value="Bwesigye">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input value="Treasure">
            </div>
            <div class="form-group full">
                <label>Email</label>
                <input value="treasurebwesigye@gmail.com" type="email">
            </div>
            <div class="form-group full">
                <label>Currency</label>
                <select>
                    <option selected>Ugx ($)</option>
                </select>
            </div>
        </div>
        <button class="btn btn-primary" style="margin-top:14px;width:100%">Save Changes</button>
    </div>

    <!-- Password & Security -->
    <div class="card" style="background-color: #0F1C32;">
        <div class="card-title" style="color:white"><span>🔒</span> Password & Security</div>
        <div class="form-grid">
            <div class="form-group full">
                <label>Current Password</label>
                <input type="password" placeholder="••••••••">
            </div>
            <div class="form-group full">
                <label>New Password</label>
                <input type="password" placeholder="••••••••">
            </div>
            <div class="form-group full">
                <label>Confirm Password</label>
                <input type="password" placeholder="••••••••">
            </div>
        </div>
        <button class="btn btn-info" style="margin-top:14px;width:100%;justify-content:center">Update Password</button>
        
        <hr class="divider">
        
        <div class="card-title" style="margin-bottom:12px; color:white;"><span>👥</span> Role & Access</div>
        <div style="display:flex;align-items:center;gap:12px;background:var(--bg3);padding:14px;border-radius:12px">
            <div style="font-size:22px">🛡️</div>
            <div>
                <div style="font-size:14px;font-weight:500; color:white;">Premium User</div>
                <div style="font-size:12px;color:var(--text3)">Full access to all modules & reports</div>
            </div>
            <span class="pill pill-gold" style="margin-left:auto">Active</span>
        </div>
    </div>
</div>