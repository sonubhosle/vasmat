<?php
include 'admin/includes/db.php';

// Fetch Announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

// Fetch News
$news = $conn->query("SELECT * FROM news ORDER BY event_date DESC");
?>

<section class="bg-slate-50 py-10 px-4">
  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- LEFT: PRINCIPAL CARD -->
    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center text-center hover:shadow-xl transition">
      <img src="https://via.placeholder.com/120" class="w-[100px] h-[100px] bg-slate-100 rounded-full object-cover mb-4" />
      <h3 class="text-xl font-bold text-slate-800">Principals Desk</h3>
      <p class="text-slate-600 text-sm mt-2">
        Welcome to Mit College, an institution where excellence is a tradition.
      </p>
      <a href="" class=" mt-3 text-sm mb-4 bg-slate-900 text-white p-3 rounded-lg" >PRINCIPAL DESK</a>
    </div>

    <!-- RIGHT -->
    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- IMPORTANT ANNOUNCEMENTS -->
      <div class="bg-white rounded-xl shadow-lg p-5 overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center gap-2">
         <i class='bx bxs-megaphone'></i> Important Announcements
        </h3>

      <div class="h-64 overflow-hidden relative ">
  <div class="animate-scroll flex flex-col gap-2">
    <?php while($row = $announcements->fetch_assoc()): ?>
      <a href="uploads/<?= htmlspecialchars($row['pdf']) ?>" target="_blank"
         class="flex items-start gap-3  py-1 rounded-lg transition">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-white animate-pulse">
           <?= htmlspecialchars($row['badge']) ?>
        </span>
        <p class="text-slate-700 text-sm font-semibold"><?= htmlspecialchars($row['title']) ?></p>
        
      </a>
    <?php endwhile; ?>
  </div>
</div>

      </div>

      <!-- NEWS -->
      <div class="bg-white rounded-xl shadow-lg p-5">
        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center gap-2">
          <i class='bx bx-news'></i> News, Events & Conferences
        </h3>

        <ul class="space-y-4">
          <?php if($news->num_rows > 0): ?>
            <?php while ($row = $news->fetch_assoc()): ?>
            <li class=" pl-3 hover:bg-blue-50 transition p-2 rounded-lg">
              <h4 class="text-sm font-semibold text-slate-800">
                <?= htmlspecialchars($row['title']) ?>
              </h4>
              <p class="text-xs text-slate-500">
                <?= date("F d, Y", strtotime($row['event_date'])) ?>
              </p>
              <?php if(!empty($row['pdf'])): ?>
              <a href="uploads/<?= $row['pdf'] ?>" target="_blank" class="text-blue-600 text-xs underline mt-1 inline-block">View PDF</a>
              <?php endif; ?>
            </li>
            <?php endwhile; ?>
          <?php else: ?>
            <li class="text-gray-400 text-sm">No news available</li>
          <?php endif; ?>
        </ul>
      </div>

    </div>
  </div>
</section>

<!-- SAY NO RAGGING SECTION -->
<section class="bg-gradient-to-tb from -emerald py-12 ">
  <div class="px-10 flex flex-col md:flex-row items-start md:items-center gap-6">

    <!-- Left: Image / Illustration -->
    <div class="flex-shrink-0">
      <img 
        src="https://static.wixstatic.com/media/2399f1_ab66c298cdee491597e13bd641fb9125~mv2.jpg/v1/fill/w_630,h_500,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/2399f1_ab66c298cdee491597e13bd641fb9125~mv2.jpg" 
        alt="Say No Ragging" 
        class="w-[300px] h-[300px] rounded-xl  object-cover animate-fade-down"
      >
    </div>

    <!-- Right: Content -->
    <div class="flex-1 bg-white rounded-xl shadow-lg p-6 animate-fade-down">
      <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
        🚫 Say No Ragging
      </h2>
      <p class="text-slate-700 mb-4 text-sm md:text-base">
        Ragging is strictly prohibited in our institution. Every student has the right to a safe, respectful, and harassment-free environment.  
        We encourage students to report any incident immediately to the authorities.  
      </p>
      <ul class="list-disc list-inside space-y-2 text-sm text-slate-700">
        <li>Respect your peers and seniors at all times.</li>
        <li>Report any ragging incidents immediately.</li>
        <li>Be aware of the anti-ragging regulations of the college.</li>
        <li>Help us maintain a safe and positive environment.</li>
      </ul>
      <a href="contact.php" class="inline-block mt-4 px-6 py-2 bg-amber-500 text-white font-semibold rounded-xl shadow hover:bg-amber-600 transition">
        Report an Incident
      </a>
    </div>

  </div>
</section>
