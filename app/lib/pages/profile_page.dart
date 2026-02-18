import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'login_page.dart';

class ProfilePage extends StatelessWidget {
  const ProfilePage({super.key});

  @override
  Widget build(BuildContext context) {
    const backgroundColor = Color(0xFFF6F4EF);
    const accentRed = Color(0xFF8B1E2D);
    const secondaryBlue = Color(0xFF163A6F);

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        backgroundColor: backgroundColor,
        elevation: 0,
        title: Text(
          "Profile",
          style: GoogleFonts.inter(
            fontWeight: FontWeight.w700,
            color: Colors.black,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ===== PROFILE HEADER CARD =====
            Container(
              padding: const EdgeInsets.all(22),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(22),
                gradient: const LinearGradient(
                  colors: [accentRed, secondaryBlue],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
              child: Row(
                children: [
                  const CircleAvatar(
                    radius: 34,
                    backgroundColor: Colors.white,
                    child: Icon(Icons.person, size: 36, color: Colors.black87),
                  ),
                  const SizedBox(width: 16),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        "John Anderson",
                        style: GoogleFonts.inter(
                          fontSize: 20,
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        "Parent Account",
                        style: GoogleFonts.inter(color: Colors.white70),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const SizedBox(height: 28),

            _sectionTitle("Child Information"),
            const SizedBox(height: 12),

            _infoCard(
              child: Column(
                children: const [
                  _ProfileRow(label: "Child Name", value: "Michael Anderson"),
                  _ProfileRow(label: "Age", value: "13"),
                  _ProfileRow(label: "Team", value: "U14 FC Rochester"),
                ],
              ),
            ),

            const SizedBox(height: 28),

            _sectionTitle("Account Settings"),
            const SizedBox(height: 12),

            _settingsCard(
              icon: Icons.email_outlined,
              title: "Email",
              subtitle: "john.anderson@email.com",
            ),
            _settingsCard(
              icon: Icons.lock_outline,
              title: "Change Password",
              subtitle: "Update your password",
            ),
            _settingsCard(
              icon: Icons.notifications_none,
              title: "Notifications",
              subtitle: "Manage alerts and reminders",
            ),

            const SizedBox(height: 28),

            _sectionTitle("Support"),
            const SizedBox(height: 12),

            _settingsCard(
              icon: Icons.help_outline,
              title: "Help Center",
              subtitle: "FAQs and contact support",
            ),
            _settingsCard(
              icon: Icons.info_outline,
              title: "About SportsPlay",
              subtitle: "App version 1.0.0",
            ),

            const SizedBox(height: 40),

            // ===== LOGOUT BUTTON =====
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: accentRed,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
                onPressed: () {
                  Navigator.pushAndRemoveUntil(
                    context,
                    MaterialPageRoute(builder: (_) => const LoginPage()),
                    (route) => false,
                  );
                },
                child: Text(
                  "Log Out",
                  style: GoogleFonts.inter(
                    fontWeight: FontWeight.w600,
                    fontSize: 16,
                    color: Colors.white,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 50),
          ],
        ),
      ),
    );
  }

  Widget _sectionTitle(String title) {
    return Text(
      title,
      style: GoogleFonts.inter(fontSize: 18, fontWeight: FontWeight.w700),
    );
  }

  Widget _infoCard({required Widget child}) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: child,
    );
  }

  Widget _settingsCard({
    required IconData icon,
    required String title,
    required String subtitle,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Row(
        children: [
          Icon(icon, color: Colors.black87),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: GoogleFonts.inter(fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 4),
                Text(
                  subtitle,
                  style: GoogleFonts.inter(
                    fontSize: 13,
                    color: Colors.grey[600],
                  ),
                ),
              ],
            ),
          ),
          const Icon(Icons.chevron_right),
        ],
      ),
    );
  }
}

class _ProfileRow extends StatelessWidget {
  final String label;
  final String value;

  const _ProfileRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        children: [
          Text(
            "$label: ",
            style: GoogleFonts.inter(fontWeight: FontWeight.w600),
          ),
          Expanded(child: Text(value, style: GoogleFonts.inter())),
        ],
      ),
    );
  }
}
