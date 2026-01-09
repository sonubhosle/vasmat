<div class="relative w-full  overflow-hidden ">
    <!-- Slides -->
    <div id="carousel" class="relative w-full h-[500px]">
        <img src="https://i.postimg.cc/qMmPDmzz/VED06122.jpg" class="absolute z-10 inset-0 w-full h-full object-cover  transition-opacity duration-700 opacity-100">
        <img src="https://i.postimg.cc/6qmjJrXb/DSC06943.jpg" class="absolute z-10 inset-0 w-full h-full object-cover transition-opacity duration-700 opacity-0">
        <img src="https://i.postimg.cc/VLzZ8X3C/VED06462.jpg" class="absolute z-10 inset-0 w-full h-full object-cover transition-opacity duration-700 opacity-0">
    </div>

    <!-- Arrows -->
    <button id="prev" class="absolute top-1/2 left-4 -translate-y-1/2 bg-black/40 text-white p-2 rounded-full hover:bg-black/60 transition">
        &#10094;
    </button>
    <button id="next" class="absolute top-1/2 right-4 -translate-y-1/2 bg-black/40 text-white p-2 rounded-full hover:bg-black/60 transition">
        &#10095;
    </button>
</div>

<script>
const slides = document.querySelectorAll('#carousel img');
let current = 0;
let interval = 3000;

function showSlide(index){
    slides.forEach((slide, i)=>{
        slide.classList.remove('opacity-100');
        slide.classList.add('opacity-0');
        if(i === index) slide.classList.add('opacity-100');
    });
}

let slideInterval = setInterval(()=>{
    current = (current + 1) % slides.length;
    showSlide(current);
}, interval);

document.getElementById('prev').addEventListener('click', ()=>{
    current = (current - 1 + slides.length) % slides.length;
    showSlide(current);
});

document.getElementById('next').addEventListener('click', ()=>{
    current = (current + 1) % slides.length;
    showSlide(current);
});
</script>
