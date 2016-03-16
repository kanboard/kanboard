<?php
namespace Kanboard\Analytic;
use Kanboard\Core\Base;
/**
 * Category Distribution
 *
 * @package  analytic
 * @author   Frederic Guillot
 */
class CategoryDistributionAnalytic extends Base
{
    /**
     * Build report
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function build($project_id)
    {
        $metrics = array();
        $total = 0;
        $categories = $this->category->getAll($project_id);
        foreach ($categories as $category) {
            $nb_tasks = $this->taskFinder->countByCategoryId($project_id, $category['id']);
            $total += $nb_tasks;
            $metrics[] = array(
                'category_name' => $category['name'],
                'nb_tasks' => $nb_tasks,
            );
        }
        if ($total === 0) {
            return array();
        }
        foreach ($metrics as &$metric) {
            $metric['percentage'] = round(($metric['nb_tasks'] * 100) / $total, 2);
        }
        return $metrics;
    }
}
