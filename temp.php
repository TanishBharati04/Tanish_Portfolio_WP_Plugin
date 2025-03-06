<div class="wrap">
            <h1><?php _e('Import/Export Projects', 'tanish-portfolio'); ?></h1>
            <div class="tanish-container">
                <!-- Export Section -->
                <div class="tanish-export">
                    <h2><?php _e('Export Projects', 'tanish-portfolio'); ?></h2>
                    <div class="column-to-be-export">
                        <label for="export_columns">Which columns should be exported?</label>
                        <select id="export_columns">
                            <option value="all"><?php _e('All Columns', 'tanish-portfolio'); ?></option>
                            <option value="title"><?php _e('Title', 'tanish-portfolio'); ?></option>
                            <option value="description"><?php _e('Description', 'tanish-portfolio'); ?></option>
                            <option value="category"><?php _e('Category', 'tanish-portfolio'); ?></option>
                            <option value="tags"><?php _e('Tags', 'tanish-portfolio'); ?></option>
                            <option value="start_date"><?php _e('Start Date', 'tanish-portfolio'); ?></option>
                            <option value="end_date"><?php _e('End Date', 'tanish-portfolio'); ?></option>
                        </select>
                    </div>

                    <div class="export-based-on-cat">
                        <label for="export_category">Which category should be exported?</label>
                        <select id="export_category">
                            <option value="all"><?php _e('All Categories', 'tanish-portfolio'); ?></option>
                            <!-- Categories will be populated dynamically -->
                        </select>
                    </div>

                    <div class="export-based-on-tag">
                        <label for="export_tags">Which tag type should be exported?</label>
                        <select id="export_tags">
                            <option value="all"><?php _e('All Tags', 'tanish-portfolio'); ?></option>
                            <!-- Tags will be populated dynamically -->
                        </select>
                    </div>

                    <button id="download_csv"><?php _e('Download CSV', 'tanish-portfolio'); ?></button>
                    <div id="export_progress"></div>
                </div>

                <!-- Import Section -->
                <div class="tanish-import">
                    <h2>Import Projects</h2>
                    <form id="import_form">
                        <div>
                            <label>Title:</label>
                            <input type="text" id="import_title" name="title" required>
                        </div>

                        <div>
                            <label>Description:</label>
                            <textarea id="import_description" name="description"></textarea>
                        </div>

                        <div>
                            <label>Categories (comma-separated):</label>
                            <input type="text" id="import_category" name="category" placeholder="E.g., Web Development, UI/UX">
                        </div>

                        <div>
                            <label>Tags (comma-separated):</label>
                            <input type="text" id="import_tags" name="tags" placeholder="E.g., React, WordPress, PHP">
                        </div>

                        <div>
                            <label>Start Date:</label>
                            <input type="date" id="import_start_date" name="start_date">
                        </div>

                        <div>
                            <label>End Date:</label>
                            <input type="date" id="import_end_date" name="end_date">
                        </div>

                        <div>
                            <label>Project Image:</label>
                            <input type="file" id="import_image" name="image">
                        </div>

                        <div class="upload">
                            <button type="submit" class="upload-btn">Upload</button>
                        </div>
                    </form>
                    <div id="import_status"></div>
                </div>

            </div>
        </div>