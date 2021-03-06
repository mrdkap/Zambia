(setq org-publish-project-alist
      '(("Zambia-html"
	 :base-directory "~/flea/www/prog/Documentation/Source"
	 :base-extension "org"
	 :publishing-directory "~/flea/www/prog/Documentation"
	 :publishing-function org-html-publish-to-html
	 :preserve-breaks t
	 :sub-superscript nil
	 :timestamps nil
	 :timestamp nil
	 :creator-info nil
	 :headline-levels 3
	 :section-numbers t
	 :fixed-width nil
	 :tables t
	 :tables-auto-headline t
	 :special-strings t
	 :todo-keywords nil
	 :tasks nil
	 :tags t
	 :emphasize t
	 :author "Percy"
	 :email "NELA.Percy@gmail.com"
	 :author-info t
	 :email-info t
	 :skip-before-1st-heading nil
	 :drawers t
	 :footnotes t
	 :priority t
	 :table-of-contents t
	 :html-table-tag "<TABLE border=\"1\" rules=\"all\" frame=\"border\">"
	 :style "<link rel=\"stylesheet\" href=\"../webpages/Common.css\" type=\"text/css\"/>"
	 :html-preamble t)

	("Zambia-pdf"
	 :base-directory "~/flea/www/prog/Documentation/Source"
	 :base-extension "org"
	 :publishing-directory "~/flea/www/prog/Documentation"
	 :publishing-function org-latex-publish-to-pdf
	 :preserve-breaks t
	 :sub-superscript nil
	 :timestamps nil
	 :timestamp nil
	 :creator-info nil
	 :headline-levels 3
	 :section-numbers t
	 :fixed-width nil
	 :tables t
	 :tables-auto-headline t
	 :special-strings t
	 :todo-keywords nil
	 :tasks nil
	 :tags t
	 :emphasize t
	 :author "Percy"
	 :email "NELA.Percy@gmail.com"
	 :author-info t
	 :email-info t
	 :skip-before-1st-heading nil
	 :drawers t
	 :footnotes t
	 :priority t
	 :link-validation-function org-publish-validate-link
	 :auto-sitemap t
	 :sitemap-title "Site Map"
	 :sitemap-sort-folders first
	 :table-of-contents t)

	("Zambia-images"
	 :base-directory "~/flea/www/prog/Documentation/Source/Images"
	 :base-extension "jpg\\|gif\\|png"
	 :publishing-directory "~/flea/www/prog/Documentation/Images"
	 :publishing-function org-publish-attachment)

	("Zambia-other"
	 :base-directory "~/flea/www/prog/Documentation/Source"
	 :base-extension "css"
	 :publishing-directory "~/flea/www/prog/Documentation"
	 :publishing-function org-publish-attachment)

	("Zambia" :components ("Zambia-other" "Zambia-images" "Zambia-pdf" "Zambia-html"))))
