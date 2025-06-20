<nav class="sidebar">
    <div class="logo d-flex justify-content-between">
        <a class="large_logo" href="{{ route('dashboard.alternative') }}"><img src="{{ asset('assets/img/logo.png') }}" alt=""></a>
        <a class="small_logo" href="{{ route('dashboard.alternative') }}"><img src="{{ asset('assets/img/mini_logo.png') }}" alt=""></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu">
        <li class="">
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/dashboard.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>User Management </span>
                </div>
            </a>
            <ul>
                <li><a href="{{ route('dashboard.default') }}">Default</a></li>
              <li><a href="{{ route('dashboard.dark') }}">Dark Sidebar</a></li>
              <li><a href="{{ route('dashboard.alternative') }}">Light Sidebar</a></li>
            </ul>
        </li>
        <li class="">
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/2.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>Application </span>
                </div>
            </a>
            <ul>
              <li><a href="{{ route('application.editor') }}">editor</a></li>
              <li><a href="{{ route('application.mailbox') }}">Mail Box</a></li>
              <li><a href="{{ route('application.chat') }}">Chat</a></li>
              <li><a href="{{ route('application.faq') }}">FAQ</a></li>
            </ul>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/3.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Pages</span>
            </div>
            </a>
            <ul>
              <li><a href="/login">Login</a></li>
              <li><a href="{{ route('register') }}">Register</a></li>
              <li><a href="{{ route('error.400') }}">Error 404</a></li>
              <li><a href="{{ route('error.500') }}">Error 500</a></li>
              <li><a href="{{ route('password.request') }}">Forgot Password</a></li>
              <li><a href="{{ route('pages.gallery') }}">Gallery</a></li>
            </ul>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/4.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Admins</span>
            </div>
            </a>
            <ul>
              <li><a href="{{ route('admins.list') }}">Admin List</a></li>
              <li><a href="{{ route('admins.create') }}">Add New Admin</a></li>
            </ul>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/11.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Role & Permissions</span>
            </div>
            </a>
            <ul>
              <li><a href="{{ route('settings.module') }}">Module Setting</a></li>
              <li><a href="{{ route('permissions.role') }}">Role & Permissions</a></li>
            </ul>
        </li>
        <li class="">
            <a  href="{{ route('ui.navs') }}" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/12.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Navs</span>
            </div>
            </a>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/5.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Users</span>
            </div>
            </a>
            <ul>
              <li><a href="{{ route('users.list') }}">Users List</a></li>
              <li><a href="{{ route('users.create') }}">Add New User</a></li>
            </ul>
        </li>
        <li>
            <a href="{{ route('pages.builder') }}" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/6.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>Builder </span>
                </div>
            </a>
        </li>
        <li class="">
            <a  href="{{ route('pages.invoice') }}" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/7.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Invoice</span>
            </div>
            </a>
        </li>
        <li class="">
            <a  class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                  <img src="{{ asset('assets/img/menu-icon/8.svg') }}" alt="">
              </div>
              <div class="nav_title">
                  <span>forms</span>
              </div>
            </a>
            <ul>
              <li><a href="{{ route('forms.basic-elements') }}">Basic Elements</a></li>
              <li><a href="{{ route('forms.groups') }}">Groups</a></li>
              <li><a href="{{ route('forms.max-length') }}">Max Length</a></li>
              <li><a href="{{ route('forms.layouts') }}">Layouts</a></li>
            </ul>
          </li>
          <li class="">
              <a href="{{ route('pages.board') }}" aria-expanded="false">
                  <div class="nav_icon_small">
                      <img src="{{ asset('assets/img/menu-icon/9.svg') }}" alt="">
                  </div>
                  <div class="nav_title">
                      <span>Board</span>
                  </div>
              </a>
          </li>
        <li class="">
            <a  href="{{ route('pages.calendar') }}" aria-expanded="false">
              <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/10.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Calander</span>
            </div>
            </a>
        </li>
        <li class="">
            <a  class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                  <img src="{{ asset('assets/img/menu-icon/11.svg') }}" alt="">
              </div>
              <div class="nav_title">
                  <span>Themes</span>
              </div>
            </a>
            <ul>
              <li><a href="{{ route('themes.dark-sidebar') }}">Dark Sidebar</a></li>
              <li><a href="{{ route('themes.light-sidebar') }}">light Sidebar</a></li>
            </ul>
        </li>
        <li class="">
            <a  class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                  <img src="{{ asset('assets/img/menu-icon/12.svg') }}" alt="">
              </div>
              <div class="nav_title">
                  <span>General</span>
              </div>
            </a>
            <ul>
              <li><a href="{{ route('general.minimized-aside') }}">Minimized Aside</a></li>
              <li><a href="{{ route('general.empty-page') }}">Empty page</a></li>
              <li><a href="{{ route('general.fixed-footer') }}">Fixed Footer</a></li>
            </ul>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/13.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Products</span>
            </div>
            </a>
            <ul>
              <li><a href="{{ route('products.list') }}">Products</a></li>
              <li><a href="{{ route('products.show') }}">Product Details</a></li>
              <li><a href="{{ route('products.cart') }}">Cart</a></li>
              <li><a href="{{ route('products.checkout') }}">Checkout</a></li>
            </ul>
          </li>
        <li class="">
          <a   class="has-arrow" href="#" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/14.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Icons</span>
            </div>
          </a>
          <ul>
            <li><a href="{{ route('icons.fontawesome') }}">Fontawesome Icon</a></li>
            <li><a href="{{ route('icons.themefy') }}">themefy icon</a></li>
          </ul>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/15.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>Animations</span>
                </div>
            </a>
            <ul>
                <li><a href="{{ route('animations.wow') }}">Animate</a></li>
                <li><a href="{{ route('animations.scroll-reveal') }}">Scroll Reveal</a></li>
                <li><a href="{{ route('animations.tilt') }}">Tilt Animation</a></li>
                
            </ul>
          </li>
          <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/16.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>Components</span>
                </div>
            </a>
            <ul>
              <li><a href="{{ route('components.accordion') }}">Accordions</a></li>
              <li><a href="{{ route('components.scrollable') }}">Scrollable</a></li>
              <li><a href="{{ route('components.notification') }}">Notifications</a></li>
              <li><a href="{{ route('components.carousel') }}">Carousel</a></li>
              <li><a href="{{ route('components.pagination') }}">Pagination</a></li>
            </ul>
          </li>

          <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/17.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>Table</span>
                </div>
            </a>
            <ul>
                <li><a href="{{ route('tables.data') }}">Data Tables</a></li>
                <li><a href="{{ route('tables.bootstrap') }}">Bootstrap</a></li>
            </ul>
          </li>
          <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
                <div class="nav_icon_small">
                    <img src="{{ asset('assets/img/menu-icon/18.svg') }}" alt="">
                </div>
                <div class="nav_title">
                    <span>Cards</span>
                </div>
            </a>
            <ul>
                <li><a href="{{ route('cards.basic') }}">Basic Card</a></li>
                <li><a href="{{ route('cards.theme') }}">Theme Card</a></li>
                <li><a href="{{ route('cards.draggable') }}">Draggable Card</a></li>
            </ul>
          </li>


        <li class="">
          <a   class="has-arrow" href="#" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/19.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Charts</span>
            </div>
          </a>
          <ul>
            <li><a href="{{ route('charts.chartjs') }}">ChartJS</a></li>
            <li><a href="{{ route('charts.apex') }}">Apex Charts</a></li>
            <li><a href="{{ route('charts.sparkline') }}">Chart sparkline</a></li>
            <li><a href="{{ route('charts.am') }}">am-charts</a></li>
            <li><a href="{{ route('charts.nvd3') }}">nvd3 charts.</a></li>
          </ul>
        </li>
        <li class="">
            <a   class="has-arrow" href="#" aria-expanded="false">
              <div class="nav_icon_small">
                  <img src="{{ asset('assets/img/menu-icon/20.svg') }}" alt="">
              </div>
              <div class="nav_title">
                  <span>UI Kits </span>
              </div>
            </a>
            <ul>
              <li><a href="{{ route('uikits.colors') }}">colors</a></li>
              <li><a href="{{ route('uikits.alerts') }}">Alerts</a></li>
              <li><a href="{{ route('uikits.buttons') }}">Buttons</a></li>
              <li><a href="{{ route('uikits.modal') }}">modal</a></li>
              <li><a href="{{ route('uikits.dropdowns') }}">Droopdowns</a></li>
              <li><a href="{{ route('uikits.badges') }}">Badges</a></li>
              <li><a href="{{ route('uikits.loading-indicators') }}">Loading Indicators</a></li>
              <li><a href="{{ route('uikits.color-plate') }}">Color Plate</a></li>
              <li><a href="{{ route('uikits.typography') }}">Typography</a></li>
              <li><a href="{{ route('uikits.datepicker') }}">Date Picker</a></li>
            </ul>
          </li>

        <li class="">
          <a   class="has-arrow" href="#" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/21.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Widgets</span>
            </div>
          </a>
          <ul>
            <li><a href="{{ route('widgets.chart-box-1') }}">Chart Boxes 1</a></li>
            <li><a href="{{ route('widgets.profilebox') }}">Profile Box</a></li>
          </ul>
        </li>
        

        <li class="">
          <a   class="has-arrow" href="#" aria-expanded="false">
            <div class="nav_icon_small">
                <img src="{{ asset('assets/img/menu-icon/12.svg') }}" alt="">
            </div>
            <div class="nav_title">
                <span>Maps</span>
            </div>
          </a>
          <ul>
            <li><a href="{{ route('maps.js') }}">Maps JS</a></li>
            <li><a href="{{ route('maps.vector') }}">Vector Maps</a></li>
          </ul>
        </li>


      </ul>
</nav>