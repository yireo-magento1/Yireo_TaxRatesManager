# TODO
- Do not use cache when collecting new rates via cronjob
- Add integration tests
- Include composer library while packaging
- Test installation with and without composer

## For later
- Create proper transactional email
- What if somebody configures a feed that delivers 404 or 500 error

## Video tutorials
- Installing the extension via composer
    - Commit composer file
- Installing the extension manually
- Fixing rates manually
    - Change a date and see the hint in action
- Getting started without any rates
    - Demo environment, add dates automatically
- Fixing rates automatically
    - Via the backend
    - Via cron: `magerun sys:cron:run yireo_taxratesmanager`
- Testing if the New Year changes work
    - Change feed into something else