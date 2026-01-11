<?php
$UPDATES = [
    ["text" => "Admissions Open for 2024-2025: Apply before Oct 30", "isNew" => true],
    ["text" => "EduPrime Ranked #1 Innovation College by Global Education Summit", "isNew" => false],
    ["text" => "New Robotics & AI Lab Inauguration Ceremony on Friday", "isNew" => true],
    ["text" => "International Student Exchange Program Applications are Live", "isNew" => true],
    ["text" => "Congratulations to our Football Team for winning the National Championship!", "isNew" => false],
    ["text" => "Guest Lecture: 'Future of Tech' by Industry Leaders - Nov 12", "isNew" => false]
];

// Repeat to make the marquee loop smoothly
$loopedUpdates = array_merge($UPDATES, $UPDATES, $UPDATES);
?>

<div class="bg-white border-t border-slate-100">
  <div class="flex items-stretch overflow-hidden">
    
    <!-- Label Section -->
  

    <!-- Icon Section -->
    <div class="relative z-10 shrink-0 w-12 bg-white flex items-center justify-center -skew-x-12 ">
      <!-- Replace with an icon, here simple megaphone emoji -->
      <span class="text-slate-500"><i class='bx bxs-megaphone text-[20px]'></i></span>
    </div>

    <!-- Marquee Container -->
    <div class="flex-1 overflow-hidden py-4 flex items-center relative bg-white">
      <div class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-white to-transparent z-10"></div>
      <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-white to-transparent z-10"></div>

      <div class="flex animate-marquee">
        <?php foreach($loopedUpdates as $item): ?>
          <div class="flex items-center whitespace-nowrap px-8 group cursor-pointer">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-600 mr-4 group-hover:bg-amber-500 transition-colors duration-300"></span>

            <?php if($item["isNew"]): ?>
              <span class="mr-3 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-white animate-pulse shadow-[0_0_10px_rgba(245,158,11,0.5)]">
                NEW
              </span>
            <?php endif; ?>

            <span class="text-sm font-medium text-slate-600 group-hover:text-amber-400 transition-colors duration-300 tracking-wide">
              <?= htmlspecialchars($item["text"]) ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    
  </div>
</div>


<style>
@keyframes marquee {
  0% { transform: translateX(0%); }
  100% { transform: translateX(-33%); }
}

.animate-marquee {
  display: flex;
  animation: marquee 20s linear infinite;
}
</style>