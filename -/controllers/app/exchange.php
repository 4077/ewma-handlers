<?php namespace ewma\handlers\controllers\app;

class Exchange extends \Controller
{
    public function importCat()
    {
        $catPath = $this->data('target_cat_path');

        $catsNames = p2a($catPath);

        $cat = handlers()->cats->getRootCat();

        foreach ($catsNames as $catName) {
            if (!$nestedCat = $cat->nested()->where('name', $catName)->first()) {
                $nestedCat = $cat->nested()->create([
                                                        'name' => $catName
                                                    ]);
            }

            handlers()->updatePaths($nestedCat);

            $cat = $nestedCat;
        }

        handlers()->cats->import($cat, $this->data('data'), $this->data('skip_first_level'));
    }
}
