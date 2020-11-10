

.PHONY: help install
.DEFAULT_GOAL := help


help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'


# --------------------------------------------------------------------------

phpcs: ## run phpcs
	composer run-script phpcs

phpcbf: ## run phpcbf
	composer run-script phpcbf
