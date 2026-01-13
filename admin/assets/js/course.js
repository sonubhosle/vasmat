
    console.log('Script loaded');
    console.log('createCourseBtn:', document.getElementById('createCourseBtn'));
    console.log('courseModal:', document.getElementById('courseModal'));

    // Simple modal functions - no complex setup
 function openModal(course = null) {
    const modal = document.getElementById('courseModal');

    if (!modal) {
        console.error("Modal not found");
        return;
    }

    if (course) {
        document.getElementById('modalTitle').innerText = 'Edit Course';
        document.getElementById('formAction').value = 'update';
        document.getElementById('courseId').value = course.id;
        document.getElementById('courseName').value = course.course_name;
        document.getElementById('description').value = course.description;
        document.getElementById('duration').value = course.duration;
        document.getElementById('fees').value = course.fees;

        if (course.image) {
            document.getElementById('existingImage').value = course.image;
            document.getElementById('currentImage').innerHTML = `
                <p class="text-sm mb-2">Current Image:</p>
                <img src="../../${course.image}" class="w-32 h-32 rounded">
            `;
        }
    } else {
        document.getElementById('modalTitle').innerText = 'Create New Course';
        document.getElementById('courseForm').reset();
        document.getElementById('formAction').value = 'create';
        document.getElementById('courseId').value = '';
        document.getElementById('existingImage').value = '';
        document.getElementById('currentImage').innerHTML = '';
    }

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('courseModal').classList.remove('active');
    document.getElementById('deleteModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}


    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Simple event binding - put this at the end
    document.addEventListener("DOMContentLoaded", function () {
    const createBtn = document.getElementById('createCourseBtn');

    if (createBtn) {
        createBtn.addEventListener('click', function () {
            console.log('Create button clicked');
            openModal();
        });
    } else {
        console.log("Button not found!");
    }
});

        
        // Bind close buttons
        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelModal').addEventListener('click', closeModal);
        document.getElementById('cancelDelete').addEventListener('click', closeDeleteModal);
        
        // Bind delete confirm
        document.getElementById('confirmDelete').addEventListener('click', deleteCourse);
        
        // Bind form submit
        document.getElementById('courseForm').addEventListener('submit', handleFormSubmit);
        
        // Bind image preview
        document.getElementById('image').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('currentImage').innerHTML = '';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Bind filter dropdown
        document.getElementById('dateFilterBtn').addEventListener('click', function() {
            document.getElementById('dateFilterDropdown').classList.toggle('show');
        });
        
        // Bind filter options
        document.querySelectorAll('.filter-option').forEach(option => {
            option.addEventListener('click', function() {
                const filterValue = this.getAttribute('data-filter');
                document.getElementById('dateFilter').value = filterValue;
                document.getElementById('dateFilterText').textContent = this.textContent;
                document.getElementById('dateFilterDropdown').classList.remove('show');
            });
        });
        
        // Bind apply filters
        document.getElementById('applyFilters').addEventListener('click', function() {
            const search = document.getElementById('searchInput').value.trim();
            const dateFilterValue = document.getElementById('dateFilter').value;
            
            let url = window.location.pathname + '?';
            if (search) url += `search=${encodeURIComponent(search)}&`;
            if (dateFilterValue !== 'all') url += `date_filter=${dateFilterValue}`;
            
            window.location.href = url;
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('dateFilterDropdown');
            const button = document.getElementById('dateFilterBtn');
            if (!button.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    
    // Make functions available globally for inline onclick handlers
    window.editCourse = async function(courseId) {
        try {
            const response = await fetch(`get_course.php?id=${courseId}`);
            const course = await response.json();
            if (course) {
                openModal(course);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    window.confirmDelete = function(courseId) {
        document.getElementById('deleteCourseId').value = courseId;
        document.getElementById('deleteModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('saveCourseBtn');
        const spinner = submitBtn.querySelector('.fa-spinner');
        const submitText = submitBtn.querySelector('span');
        
        submitBtn.disabled = true;
        spinner.classList.remove('hidden');
        submitText.textContent = 'Saving...';
        
        try {
            const formData = new FormData(document.getElementById('courseForm'));
            
            const response = await fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('success', result.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('error', result.message || 'An error occurred');
            }
        } catch (error) {
            showAlert('error', 'Network error. Please try again.');
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            spinner.classList.add('hidden');
            submitText.textContent = 'Save Course';
        }
    }

    async function deleteCourse() {
        const courseId = document.getElementById('deleteCourseId').value;
        const deleteBtn = document.getElementById('confirmDelete');
        const spinner = deleteBtn.querySelector('.fa-spinner');
        const deleteText = deleteBtn.querySelector('span');
        
        deleteBtn.disabled = true;
        spinner.classList.remove('hidden');
        deleteText.textContent = 'Deleting...';
        
        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', courseId);
            
            const response = await fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('success', result.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('error', result.message || 'Failed to delete course');
            }
        } catch (error) {
            showAlert('error', 'Network error. Please try again.');
            console.error('Error:', error);
        } finally {
            deleteBtn.disabled = false;
            spinner.classList.add('hidden');
            deleteText.textContent = 'Delete';
            closeDeleteModal();
        }
    }

    function showAlert(type, message) {
        const alert = document.createElement("div");
        alert.className = `px-6 py-4 rounded-xl shadow-xl text-white transform transition-all duration-500 ${
            type === "success" ? "bg-emerald-500" : "bg-amber-500"
        } alert-enter`;
        
        alert.innerHTML = `
            <div class="flex items-center gap-3">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
                <span>${message}</span>
            </div>
        `;

        document.getElementById('alertContainer').appendChild(alert);

        setTimeout(() => {
            alert.classList.remove('alert-enter');
            alert.classList.add('alert-exit');
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }
