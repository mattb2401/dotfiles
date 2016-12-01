(require 'package)
(push '("marmalade" . "http://marmalade-repo.org/packages/")
    package-archives )
(push '("melpa" . "http://melpa.milkbox.net/packages/")
    package-archives)
;(unless package-archive-contents (package-refresh-contents))
(package-initialize)

; get us out of carpal tunnel mode
(unless (package-installed-p 'evil)
  (package-install 'evil))
(evil-mode 1)        ;; enable evil-mode

(desktop-save-mode 1)
(setq desktop-restore-frames t)

(autoload 'csharp-mode "csharp-mode" "Major mode for editing C# code." t)
(setq auto-mode-alist
    (append '(("\\.cs$" . csharp-mode)) auto-mode-alist))

(require 'php-boris)

;;; This was installed by package-install.el.
;;; This provides support for the package system and
;;; interfacing with ELPA, the package archive.
;;; Move this code earlier if you want to reference
;;; packages in your .emacs.

(custom-set-variables
 ;; custom-set-variables was added by Custom.
 ;; If you edit it by hand, you could mess it up, so be careful.
 ;; Your init file should contain only one such instance.
 ;; If there is more than one, they won't work right.
 '(custom-safe-themes (quote ("0f002f8b472e1a185dfee9e5e5299d3a8927b26b20340f10a8b48beb42b55102" default))))
(custom-set-faces
 ;; custom-set-faces was added by Custom.
 ;; If you edit it by hand, you could mess it up, so be careful.
 ;; Your init file should contain only one such instance.
 ;; If there is more than one, they won't work right.
 )

(load-theme 'smyx)

(if window-system
    (tool-bar-mode -1)
)

(unless (package-installed-p 'fsharp-mode)
  (package-install 'fsharp-mode))

(add-hook 'fsharp-mode-hook
    (lambda ()
        (define-key fsharp-mode-map (kbd "M-i") 'fsharp-eval-region)
	    (define-key fsharp-mode-map (kbd "C-SPC") 'fsharp-ac/complete-at-point)))

(setq linum-format "%d ")
(add-hook 'prog-mode-hook 'relative-line-numbers-mode t)
(add-hook 'prog-mode-hook 'line-number-mode t)
(add-hook 'prog-mode-hook 'column-number-mode t)
