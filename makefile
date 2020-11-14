

.PHONY: help install
.DEFAULT_GOAL := help



help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'


# --------------------------------------------------------------------------
symlink-dev: ## Bla
	$(eval BREW_DIW_BINARY_PATH := $(shell brew --cellar diw)/$(shell brew list --versions diw | tr ' ' '\n' | tail -1)/bin/diw)

	$(shell chmod 777 $(BREW_DIW_BINARY_PATH))
	@echo "#!/usr/bin/env bash" > $(BREW_DIW_BINARY_PATH)
	@echo "" >> $(BREW_DIW_BINARY_PATH)
	@echo "DIR="$(shell pwd)"" >> $(BREW_DIW_BINARY_PATH)
	@echo "php "\$$DIR/src/DIW.php" \"\$$@"\" >> $(BREW_DIW_BINARY_PATH)





phpcs: ## run phpcs
	composer run-script phpcs

phpcbf: ## run phpcbf
	composer run-script phpcbf

doc: ## call docs
	$(MAKE) -C docs

doc-html: ## Generate html files
	$(MAKE) -C docs html
