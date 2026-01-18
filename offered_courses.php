<?php include './includes/header.php'; ?>

<?php
$SIDEBAR_LINKS = [
    ["label" => "Admission Process", "href" => "#admission", "icon" => "fa-solid fa-file-alt"],
    ["label" => "Scholarships", "href" => "#scholarships", "icon" => "fa-solid fa-graduation-cap"],
    ["label" => "Fee Structure", "href" => "#fees", "icon" => "fa-solid fa-money-bill-wave"],

];
?>

<div class="w-fll px-4 sm:px-6 lg:px-8 py-20">
    <div class="flex flex-col lg:flex-row gap-12">
     
                <div class="space-y-6 lg:w-[70%] space-y-24">
                    <div class="mb-12">
                        <div class="flex items-center gap-4 mb-4">
                            <span
                                class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-400">Curriculum</span>
                        </div>
                        <h2 class="text-4xl  font-black text-slate-900 mb-6 ">
                            Offered <span class="italic font-serif">Courses</span>
                        </h2>

                    </div>
                    <div class=" border-2 border-slate-200 ">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b-2 border-slate-200">
                                    <th
                                        class="px-8 py-6 text-[10px] uppercase tracking-widest font-black text-slate-500 border-r-2 border-slate-200">
                                        Courses
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[10px] uppercase tracking-widest font-black text-slate-500 border-r-2 border-slate-200">
                                        Eligibility Criteria</th>
                                    <th
                                        class="px-8 py-6 text-[10px] uppercase tracking-widest font-black text-slate-500 border-r-2 border-slate-200">
                                        Duration
                                    </th>
                                    <th
                                        class="px-8 py-6 text-[10px] uppercase tracking-widest font-black text-slate-500">
                                        Capacity
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-slate-200">

                                <tr key=class="group hover:bg-white/80 transition-all duration-300">
                                    <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <div class="flex items-center gap-4">

                                            <div>
                                                <div class="text-base font-semibold text-slate-600 leading-tight">
                                                    B.Sc. (Comp. Sci.)
                                                </div>

                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <p class="max-w-xs text-sm text-slate-600 font-semibold leading-relaxed">
                                            12 th Science (Any Group)
                                        </p>
                                    </td>
                                    <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <span
                                            class="inline-flex   text-sm font-bold text-slate-600 border-2 border-slate-100 whitespace-nowrap">
                                            3 Years
                                        </span>
                                    </td>
                                    <td class="px-8 py-4 bg-amber-50/10">
                                        <div
                                            class="text-xl font-semibold text-slate-600 group-hover:text-amber-700 transition-colors">
                                            80
                                        </div>

                                    </td>
                                </tr>
                                <tr key=class="group hover:bg-white/80 transition-all duration-300">
                                    <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <div class="flex items-center gap-4">

                                            <div>
                                                <div class="text-base font-semibold text-slate-600 leading-tight">
                                                    BCA
                                                </div>

                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <p class="max-w-xs text-sm text-slate-600 font-semibold leading-relaxed">
                                            12 thScience (Art/Commerce/MCVC/Science) (Any Group)
                                        </p>
                                    </td>
                                    <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <span
                                            class="inline-flex   text-sm font-semibold text-slate-600 border-2 border-slate-100 whitespace-nowrap">
                                            3 Years
                                        </span>
                                    </td>
                                    <td class="px-8 py-4 bg-amber-50/10">
                                        <div
                                            class="text-xl font-semibold text-slate-600 group-hover:text-amber-700 transition-colors">
                                            80
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <aside class="lg:w-[30%] ">
            <div class="sticky top-50">
                <div class="space-y-6">
                    <div class="bg-white rounded-[2rem] p-8 border-slate-200">
                        <h3
                            class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-3">
                            <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                            Quick Navigation
                        </h3>

                        <nav class="space-y-2">
                            <?php foreach ($SIDEBAR_LINKS as $idx => $link): ?>
                                <a href="<?php echo $link['href']; ?>"
                                    class=" border border-slate-100 px-3 py-2 rounded-xl flex items-center gap-2  text-slate-600 hover:text-amber-600  transition-all duration-300 group ">
                                    <i
                                        class="<?php echo $link['icon']; ?> text-base opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all"></i>
                                    <span class="font-bold text-sm tracking-tight"><?php echo $link['label']; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                    <div class="">
                        <a href="admission.php" class="text-lg font-semibold text-white tracking-widest  bg-gradient-to-r from-amber-400 to-amber-600 w-full h-12 flex items-center justify-center rounded-xl">Get Started</a>
                    </div>
                </div>
            </div>
            </aside>
    </div>
</div>

<?php include './includes/footer.php'; ?>