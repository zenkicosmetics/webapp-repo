class Admin extends CI_Controller {
	public function __construct() {
		parent::construct();
		ci()->load->model('neighbours/neighbour_m');
	}
}
