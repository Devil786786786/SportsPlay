import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'login_page.dart';
import 'team_page.dart';
import 'schedule_page.dart';
import 'profile_page.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _currentIndex = 0;

  final Color primaryBlue = const Color(0xFF0B1E3C);
  final Color secondaryBlue = const Color(0xFF163A6F);
  final Color accentRed = const Color(0xFF8B1E2D);
  final Color backgroundColor = const Color(0xFFF6F4EF);

  late final List<Widget> _pages;

  @override
  void initState() {
    super.initState();

    _pages = [
      _buildHomeContent(),
      const TeamPage(),
      const SchedulePage(),
      const ProfilePage(),
    ];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: backgroundColor,

      appBar: AppBar(
        backgroundColor: backgroundColor,
        elevation: 0,
        title: Image.asset('assets/images/sportsplay_logo.png', height: 28),
        actions: [
          IconButton(
            tooltip: 'Log out',
            icon: const Icon(Icons.logout_rounded, color: Colors.black87),
            onPressed: () {
              Navigator.pushAndRemoveUntil(
                context,
                MaterialPageRoute(builder: (_) => const LoginPage()),
                (route) => false,
              );
            },
          ),
        ],
      ),

      // 👇 THIS IS THE IMPORTANT PART
      body: IndexedStack(index: _currentIndex, children: _pages),

      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) => setState(() => _currentIndex = index),
        selectedItemColor: primaryBlue,
        unselectedItemColor: Colors.grey,
        type: BottomNavigationBarType.fixed,
        backgroundColor: Colors.white,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.groups), label: 'Teams'),
          BottomNavigationBarItem(
            icon: Icon(Icons.calendar_month),
            label: 'Schedule',
          ),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profile'),
        ],
      ),
    );
  }

  // ===== HOME CONTENT MOVED HERE =====
  Widget _buildHomeContent() {
    return SafeArea(
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // HERO
            Padding(
              padding: const EdgeInsets.all(16),
              child: Container(
                padding: const EdgeInsets.all(22),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(22),
                  gradient: LinearGradient(
                    colors: [accentRed, secondaryBlue],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Welcome Back, John',
                      style: GoogleFonts.inter(
                        fontSize: 24,
                        fontWeight: FontWeight.w700,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Stay connected with teams,\nschedules and results.',
                      style: GoogleFonts.inter(
                        fontSize: 15,
                        color: Colors.white70,
                      ),
                    ),
                  ],
                ),
              ),
            ),

            // SECTION TITLE
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text(
                'Sports Categories',
                style: GoogleFonts.inter(
                  fontSize: 20,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),

            const SizedBox(height: 12),

            // SPORTS GRID
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: GridView.count(
                crossAxisCount: 2,
                crossAxisSpacing: 14,
                mainAxisSpacing: 14,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                children: const [
                  SportCard(icon: Icons.sports_soccer, label: 'Soccer'),
                  SportCard(icon: Icons.sports_basketball, label: 'Basketball'),
                  SportCard(icon: Icons.sports_volleyball, label: 'Volleyball'),
                  SportCard(icon: Icons.sports_hockey, label: 'Hockey'),
                ],
              ),
            ),

            const SizedBox(height: 24),

            // RESULTS
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text(
                'Latest Results',
                style: GoogleFonts.inter(
                  fontSize: 20,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),

            const SizedBox(height: 12),

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Column(
                children: const [
                  ResultCard('FC Rochester', 'North Carolina FC', '2 - 1'),
                  ResultCard('FC New York', 'FC Dakota', '3 - 3'),
                ],
              ),
            ),

            const SizedBox(height: 80),
          ],
        ),
      ),
    );
  }

  // ===== PLACEHOLDER =====
  Widget _placeholderPage(String title) {
    return Center(
      child: Text(
        title,
        style: GoogleFonts.inter(fontSize: 22, fontWeight: FontWeight.w600),
      ),
    );
  }
}

// ===== SPORT CARD =====
class SportCard extends StatelessWidget {
  final IconData icon;
  final String label;

  const SportCard({super.key, required this.icon, required this.label});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 12,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          CircleAvatar(
            radius: 28,
            backgroundColor: Colors.black.withOpacity(0.05),
            child: Icon(icon, size: 30, color: Colors.black87),
          ),
          const SizedBox(height: 12),
          Text(label, style: GoogleFonts.inter(fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}

// ===== RESULT CARD =====
class ResultCard extends StatelessWidget {
  final String teamA;
  final String teamB;
  final String score;

  const ResultCard(this.teamA, this.teamB, this.score, {super.key});

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 1,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: ListTile(
        title: Text(
          '$teamA vs $teamB',
          style: GoogleFonts.inter(fontWeight: FontWeight.w500),
        ),
        trailing: Text(
          score,
          style: GoogleFonts.inter(fontWeight: FontWeight.w700),
        ),
      ),
    );
  }
}
