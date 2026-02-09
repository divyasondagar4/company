        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3 class="sidebar-title">Quiz Categories</h3>
            </div>
            <ul class="category-list">
                <li class="category-item active">
                    <a href="dashboard.php">
                        <div class="category-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <span>All Categories</span>
                    </a>
                </li>
                <?php 
                mysqli_data_seek($categories, 0);
                $icons = ['fa-brain', 'fa-flask', 'fa-calculator', 'fa-globe', 'fa-book', 'fa-laptop-code', 'fa-music', 'fa-palette'];
                $iconIndex = 0;
                while($cat = mysqli_fetch_assoc($categories)): 
                ?>
                    <li class="category-item">
                        <a href="start_quiz.php?cat=<?= $cat['id'] ?>">
                            <div class="category-icon">
                                <i class="fas <?= $icons[$iconIndex % count($icons)] ?>"></i>
                            </div>
                            <span><?= htmlspecialchars($cat['category_name']) ?></span>
                        </a>
                    </li>
                <?php 
                $iconIndex++;
                endwhile; 
                ?>
            </ul>
        </aside>

