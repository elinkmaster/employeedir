<li <?php echo \Request::url() == url('home') ? 'class="active"' : ''; ?>>
    <a href="{{url('home')}}">
        <em class="fa fa-home">&nbsp;</em>
        Home
    </a>
</li>
<li <?php echo \Request::url() == url('time-keeping') ? 'class="active"' : ''; ?>>
    <a href="{{url('time-keeping')}}">
        <em class="fa fa-clock-o">&nbsp;</em>
        Offline Break Logger
     </a>
 </li><li <?php echo \Request::url() == url('coaching-session') ? 'class="active"' : ''; ?>>
    <a href="{{url('coaching-session')}}">
        <em class="fa fa-cogs">&nbsp;</em>
        Linking Sessions
     </a>
 </li>
<li <?php echo \Request::url() == url('employees') ? 'class="active"' : ''; ?>>
    <a href="{{url('employees')}}">
        <em class="fa fa-users">&nbsp;</em>
        Employees 
     </a>
 </li>
 <li <?php echo \Request::url() == url('leave/create') ? 'class="active"' : ''; ?>>
    <a href="{{url('leave/')}}">
        <em class="fa fa-calendar">&nbsp;</em>
        Leaves 
     </a>
 </li>
 <li <?php echo \Request::url() == url('referral/create') ? 'class="active"' : ''; ?>>
    <a href="{{url('referral/create')}}">
        <em class="fa fa-user-plus">&nbsp;</em>
        Job Referral
     </a>
 </li>

<li <?php echo \Request::url() == url('events/calendar') ? 'class="active"' : ''; ?>>
    <a href="{{url('events/calendar')}}">
        <em class="fa fa-calendar">&nbsp;</em>
        Events Calendar
    </a>
</li>
@auth
    @if(Auth::user()->leaveRequestCount() > 0)
        <!--<li <?php echo \Request::url() == url('leave') ? 'class="active"' : ''; ?>>
            <a href="{{url('leave')}}">
                <em class="fa fa-calendar">&nbsp;</em>
                Leave Requests&nbsp;&nbsp;<span class="badge label-danger">{{ Auth::user()->leaveRequestCount() }}</span>
            </a>
        </li>-->
    @endif
    @if(Auth::user()->usertype == 2 || Auth::user()->usertype == 3)
    <li <?php echo \Request::url() == url('leave') ? 'class="active"' : ''; ?>>
        <a href="\for-approval">
            <em class="fa fa-calendar">&nbsp;</em>
            Team Leave Request&nbsp;&nbsp;<span class="badge label-danger"></span>
        </a>
    </li>
    <li <?php echo \Request::url() == url('sup-view') ? 'class="active"' : ''; ?>>
    <a href="{{url('sup-view')}}">
        <em class="fa fa-clock-o">&nbsp;</em>
        Break Management 
     </a>
    </li>
    @endif
@endauth

<li >
   <a target="_blank" href="{{ url('/public/img/company-hierarchy.jpeg') }}">
        <span class="fa fa-sitemap">
        
        </span>
        Employee hierarchy
    </a>
</li>

@guest
<li>
    <a href="{{ route('login')}}">
        <em class="fa fa-sign-in">&nbsp;</em>
        Login
    </a>
</li>
@endguest
@auth

<li>
    <a href="{{ route('logout')}}">
        <em class="fa fa-power-off">&nbsp;</em>
        Logout
    </a>
</li>
@endauth