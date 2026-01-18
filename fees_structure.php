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
     
                <div class="space-y-6 w-full space-y-24">
                    <div class="mb-12">
                        <div class="flex items-center gap-4 mb-4">
                            <span
                                class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-400">Curriculum</span>
                        </div>
                        <h2 class="text-4xl  font-black text-slate-900 mb-6 ">
                            Fees <span class="italic font-serif">Structure</span>
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
                                    <th class="px-8 py-6 text-[10px] uppercase tracking-widest font-black text-slate-500 border-r-2 border-slate-200">
                                        Eligibility Criteria
                                    </th>
                                    <th class="px-8 py-6 text-[10px] uppercase tracking-widest font-black text-slate-500 border-r-2 border-slate-200">
                                        First Year
                                    </th>
                                     <th class="px-8 py-6 text-[10px] uppercase tracking-widest font-black text-slate-500 border-r-2 border-slate-200">
                                        Second Year
                                    </th>
                                    <th class="px-8 py-6 text-[10px] uppercase tracking-widest font-black text-slate-500 border-r-2 border-slate-200">
                                        Third Year
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-slate-200 bg-white/80">

                                <tr key=class="group bg-white transition-all duration-300">
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
                                        <span class="inline-flex   text-sm font-bold text-slate-600 border-2 border-slate-100 whitespace-nowrap">
                                            20610
                                        </span>
                                    </td>
                                     <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <span class="inline-flex   text-sm font-bold text-slate-600 border-2 border-slate-100 whitespace-nowrap">
                                            20610
                                        </span>
                                    </td>
                                     <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <span class="inline-flex   text-sm font-bold text-slate-600 border-2 border-slate-100 whitespace-nowrap">
                                            20610
                                        </span>
                                    </td>
                                    
                                </tr>
                                <tr key=class="group bg-white transition-all duration-300">
                                    <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <div class="flex items-center gap-4">

                                                <div class="text-base font-semibold text-slate-600 leading-tight">
                                                    BCA
                                                </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <p class="max-w-xs text-sm text-slate-600 font-semibold leading-relaxed">
                                            12 thScience (Art/Commerce/MCVC/Science) (Any Group)
                                        </p>
                                    </td>
                                     <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <span class="inline-flex   text-sm font-bold text-slate-600 border-2 border-slate-100 whitespace-nowrap">
                                            20610
                                        </span>
                                    </td>
                                     <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <span class="inline-flex   text-sm font-bold text-slate-600 border-2 border-slate-100 whitespace-nowrap">
                                            20610
                                        </span>
                                    </td>
                                     <td class="px-8 py-4 border-r-2 border-slate-200">
                                        <span class="inline-flex   text-sm font-bold text-slate-600 border-2 border-slate-100 whitespace-nowrap">
                                            20610
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

    </div>
</div>

<?php include './includes/footer.php'; ?>