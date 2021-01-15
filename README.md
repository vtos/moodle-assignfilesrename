### Renaming of the uploaded files in Assign activity as a plugin for Moodle LMS

This is one of the plugins I had a chance to develop for one of the customers. The task was to rename a file uploaded by a student in an assign activity in Moodle. The customer wanted the users to see some readable names for the uploaded assign files for some reason. Probably, those files were to be further processed by some software but that's beyond the Moodle's scope. The task is to rename a submitted assign file, and a local type of plugin is a perfect tool to achieve it. Moodle emits various events and uploading files for an assignment submission is among them. See https://docs.moodle.org/dev/Events_API for the complete list of events in Moodle, and we're interested in **assignsubmission_file\event\submission_created** and **assignsubmission_file\event\submission_updated**.

### Requirements
The plugin requires Moodle version 2020061500 which is Moodle 3.9 (Build: 20200615), and works with the latest 2020110900.08 (3.10+, build: 20210108).

### Installation and setup
The basic process as for any Moodle plugin. As this is a local type of plugin you place the plugin folder into the '/local' path of your Moodle and log in as an administrator. The plugin will offer to set it up during the installation process. This is just a matter of ticking the 'Enable' box to enable/disable the plugin (i.e. handling of the assign file uploading events). The setting can be set at any time in the administration section of Moodle.

### Rules for renaming of the assign uploaded files
The pattern for a renamed file is the following (the requirements were provided by the customer):<br />**{course shortname}\_{user lastname}\_{first letter of firstname}\_{date in the 'dmY' format}\_{time in 'HMS' format}**.<br />
The date and time are referenced in the PHP's 'strftime' format, see https://www.php.net/manual/en/function.strftime.php.
<br />The example file name after renaming:<br />**math101_Doe_J_22122020_210436.pdf**.<br />The plugin is capable of adding numbers to a file name after renaming if a file with this exact name is already exists in the same assign file area. In that case the renamed file would have the name like:<br />**math101_Doe_J_22122020_210436(1).pdf**.

### Unit testing
The plugin bundles with unit tests to test composing of a file name and actual renaming of assign files in Moodle file system.
