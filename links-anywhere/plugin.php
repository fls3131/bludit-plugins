<?php

    class pluginLinksHere extends Plugin {

        public function init()
        {
            $this->customHooks = array(
                'linksHere'
            );

            // JSON database
            $jsondb = json_encode(array(
                'Bludit' => 'https://www.bludit.com',
                'Bludit PRO' => 'https://pro.bludit.com'
            ));

            // Fields and default values for the database of this plugin
            $this->dbFields = array(
                'label' => 'Links',
                'jsondb' => $jsondb
            );

            // Disable default Save and Cancel button
            $this->formButtons = false;
        }

        // Method called when a POST request is sent
        public function post()
        {
            // Get current jsondb value from database
            // All data stored in the database is html encoded
            $jsondb = $this->db['jsondb'];
            $jsondb = Sanitize::htmlDecode($jsondb);

            // Convert JSON to Array
            $links = json_decode($jsondb, true);

            // Check if the user click on the button delete or add
            if (isset($_POST['deleteLink'])) {
                // Values from $_POST
                $name = $_POST['deleteLink'];

                // Delete the link from the array
                unset($links[$name]);
            } elseif (isset($_POST['addLink'])) {
                // Values from $_POST
                $name = $_POST['linkName'];
                $url = $_POST['linkURL'];

                // Check empty string
                if (empty($name)) {
                    return false;
                }

                // Add the link
                $links[$name] = $url;
            }

            // Encode html to store the values on the database
            $this->db['label'] = Sanitize::html($_POST['label']);
            $this->db['jsondb'] = Sanitize::html(json_encode($links));

            // Save the database
            return $this->save();
        }

        // Method called on plugin settings on the admin area
        public function form()
        {
            global $L;

            $html  = '<div class="alert alert-primary" role="alert">';
            $html .= $this->description();
            $html .= '</div>';

            $html  .= '<div class="alert alert-info" role="alert">';
            $html .= $L->get("links-here-readme");
            $html .= '<code>' . htmlspecialchars($L->get("links-here-code")) . '</code>';
            $html .= '</div>';

            // New link, when the user click on save button this call the method post()
            // and the new link is added to the database
            $html .= '<h4 class="mt-3">' . $L->get('Add a new link') . '</h4>';

            $html .= '<div>';
            $html .= '<label>' . $L->get('Name') . '</label>';
            $html .= '<input name="linkName" type="text" dir="auto" class="form-control" value="" placeholder="Bludit">';
            $html .= '</div>';

            $html .= '<div>';
            $html .= '<label>' . $L->get('Url') . '</label>';
            $html .= '<input name="linkURL" type="text" dir="auto" class="form-control" value="" placeholder="https://www.bludit.com/">';
            $html .= '</div>';

            $html .= '<div>';
            $html .= '<button name="addLink" class="btn btn-primary my-2" type="submit">' . $L->get('Add') . '</button>';
            $html .= '</div>';

            // Get the JSON DB, getValue() with the option unsanitized HTML code
            $jsondb = $this->getValue('jsondb', $unsanitized = false);
            $links = json_decode($jsondb, true);

            $html .= !empty($links) ? '<h4 class="mt-3">' . $L->get('Links') . '</h4>' : '';

            foreach ($links as $name => $url) {
                $html .= '<div class="my-2">';
                $html .= '<label>' . $L->get('Name') . '</label>';
                $html .= '<input type="text" dir="auto" class="form-control" value="' . $name . '" disabled>';
                $html .= '</div>';

                $html .= '<div>';
                $html .= '<label>' . $L->get('Url') . '</label>';
                $html .= '<input type="text" dir="auto" class="form-control" value="' . $url . '" disabled>';
                $html .= '</div>';

                $html .= '<div>';
                $html .= '<button name="deleteLink" class="btn btn-secondary my-2" type="submit" value="' . $name . '">' . $L->get('Delete') . '</button>';
                $html .= '</div>';
            }

            return $html;
        }

        // Method called on the website
        public function linksHere()
        {
            // Get the JSON DB, getValue() with the option unsanitized HTML code
            $jsondb = $this->getValue('jsondb', false);
            $links = json_decode($jsondb);

            // By default the database of categories are alphanumeric sorted
            $html .= '<ul>';
            foreach ($links as $name => $url) {
                $html .= '<li>';
                $html .= '<a href="' . $url . '" target="_blank">';
                $html .= $name;
                $html .= '</a>';
                $html .= '</li>';
            }
            $html .= '</ul>';

            return $html;
        }
} ?>